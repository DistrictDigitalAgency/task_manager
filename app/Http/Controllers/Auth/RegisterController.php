<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use App\Notifications\ActivationToken;

class RegisterController extends Controller
{
    
    use \App\Http\Controllers\BasicController;
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest','feature_available:enable_user_registration'])->only('showRegistrationForm');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validation_messages = [
            'user.regex' => trans('messages.username_rules'),
            'password.regex' => trans('messages.password_rules'),
        ];

        $rules = [
            'email' => 'required|email|max:255|unique:users',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'username' => 'required|max:255|unique:users|regex:/^[a-zA-Z0-9_\.\-]*$/',
            'password' => 'required|min:6|confirmed|regex:/^.*(?=.{2,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
            'password_confirmation' => 'required',
            'designation_id' => 'sometimes|required',
            'role_id' => 'sometimes|required',
        ];

        $niceNames = array();

        if(config('config.enable_tnc') && !\Auth::check()){
            $rules['tnc'] = 'accepted';
            $niceNames = [
                'tnc' => 'terms and conditions'
            ];
        }

        $validator = Validator::make($data, $rules,$validation_messages);
        $validator->setAttributeNames($niceNames); 

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        if(config('config.enable_email_verification') && !\Auth::check()){
            $activation_token = randomString(30);
            $user->activation_token = $activation_token;
            $user->status = 'pending_activation';
            $user->save();
        } elseif(config('config.enable_account_approval') && !\Auth::check()) {
            $user->status = 'pending_approval';
            $user->save();
        } else {
            $user->status = 'active';
            $user->save();
        }

        return $user;
    }

    public function register(Request $request)
    {
        
        if(!\App\Role::whereIsDefault(1)->count() && !\Auth::check()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.no_default_role_for_user'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors(trans('messages.no_default_role_for_user'));
        }

        if(!\App\Designation::whereIsDefault(1)->count() && !\Auth::check()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.no_default_designation_for_user'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors(trans('messages.no_default_designation_for_user'));
        }


        $this->validator($request->all())->validate();

        if($request->has('g-recaptcha-response')){
            $url = "https://www.google.com/recaptcha/api/siteverify";
            $postData = array(
                'secret' => config('config.recaptcha_secret'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->getClientIp()
            );
            $gresponse = postCurl($url,$postData);

            if(!$gresponse['success']){
                if($request->has('ajax_submit')){
                    $response = ['message' => 'Please verify the captcha again!', 'status' => 'error']; 
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect()->back()->withInput()->withErrors('Please verify the captcha again!');
            }
        }

        $validation = validateCustomField('user-registration-form',$request);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        event(new Registered($user = $this->create($request->all())));

        $role = \App\Role::whereIsDefault(1)->first();
        $user->roles()->sync(($request->input('role_id')) ? explode(',',$request->input('role_id')) : (isset($role) ? [$role->id] : []));

        $designation = \App\Designation::whereIsDefault(1)->first();

        $profile = new \App\Profile;
        $profile->first_name = $request->input('first_name');
        $profile->last_name = $request->input('last_name');
        $profile->designation_id = ($request->input('designation_id') ? : (isset($designation) ? $designation->id : null));
        $user->profile()->save($profile);
        $user->notify(new ActivationToken($user));

        if($request->has('send_welcome_email')){
            $template = \App\Template::whereCategory('welcome_email')->first();
            $body = isset($template->body) ? $template->body : 'Hello [NAME], Welcome to '.config('config.application_name');
            $body = str_replace('[NAME]',$user->full_name,$body); 
            $body = str_replace('[PASSWORD]',$request->input('password'),$body);
            if(!config('config.login'))
            $body = str_replace('[USERNAME]',$user->username,$body);
            $body = str_replace('[EMAIL]',$user->email,$body);

            $mail['email'] = $user->email;
            $mail['subject'] = isset($template->subject) ? $template->subject : 'Welcome to '.config('config.application_name');

            \Mail::send('emails.email', compact('body'), function($message) use ($mail){
                $message->to($mail['email'])->subject($mail['subject']);
            });
            $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));
        }

        $data = $request->all();
        storeCustomField('user-registration-form',$user->id, $data);

        $response_msg = ['message' => trans('messages.user_registered'), 'status' => 'success']; 
        return response()->json($response_msg, 200, array('Access-Controll-Allow-Origin' => '*'));
    }
}
