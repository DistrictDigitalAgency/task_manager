<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use File;
use Image;
use Swift_SmtpTransport;
use Swift_TransportException;

class ConfigurationController extends Controller
{
    use BasicController;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localizations = array();
        foreach(config('localization') as $key => $value)
            $localizations[$key] = $value['localization'];
        $assets = ['tags'];
        $menu = 'configuration';
        return view('configuration.index',compact('localizations','assets','menu'));
    }

    public function store(Request $request){

        $validation = Validator::make($request->all(),[
            'company_name' => 'sometimes|required',
            'contact_person' => 'sometimes|required',
            'email' => 'sometimes|email|required',
            'country_id' => 'sometimes|required',
            'timezone_id' => 'sometimes|required',
            'application_name' => 'sometimes|required',
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }

            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $input = $request->all();
        foreach($input as $key => $value){
            if(!in_array($key, config('constant.ignore_var'))){
                $config = \App\Config::firstOrNew(['name' => $key]);
                if($value != config('config.hidden_value'))
                $config->value = isset($value) ? $value : null;
                $config->save();
            }
        }

        $this->logActivity(['module' => 'configuration','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.configuration').' '.trans('messages.updated'), 'status' => 'success']; 
            if($request->has('company_name'))
                $response = $this->getSetupGuide($response,'general_configuration');
            elseif($request->has('application_name'))
                $response = $this->getSetupGuide($response,'system_configuration');
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/configuration')->withSuccess(trans('messages.configuration').' '.trans('messages.updated'));  
    }

    public function mail(Request $request){

        $validation = Validator::make($request->all(),[
                'from_address' => 'required|email',
                'from_name' => 'required',
                'host' => 'required_if:driver,smtp',
                'port' => 'required_if:driver,smtp|numeric',
                'username' => 'required_if:driver,smtp',
                'password' => 'required_if:driver,smtp',
                'encryption' => 'in:ssl,tls|required_if:driver,smtp',
                'mailgun_host' => 'required_if:driver,mailgun',
                'mailgun_port' => 'required_if:driver,mailgun|numeric',
                'mailgun_username' => 'required_if:driver,mailgun',
                'mailgun_password' => 'required_if:driver,mailgun',
                'mailgun_domain' => 'required_if:driver,mailgun',
                'mailgun_secret' => 'required_if:driver,mailgun',
                'mandrill_secret' => 'required_if:driver,mandrill',
                ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        if($request->input('driver') == 'smtp'){
            $stmp = 0;
            try{
                    $transport = Swift_SmtpTransport::newInstance($request->input('host'), $request->input('port'), $request->input('encryption'));
                    $transport->setUsername($request->input('username'));
                    $transport->setPassword($request->input('password'));
                    $mailer = \Swift_Mailer::newInstance($transport);
                    $mailer->getTransport()->start();
                    $stmp =  1;
                } catch (Swift_TransportException $e) {
                    $stmp =  $e->getMessage();
                } 

            if($stmp != 1){
                if($request->has('ajax_submit')){
                    $response = ['message' => $stmp, 'status' => 'error']; 
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect()->back()->withInput()->withErrors($stmp);
            }
        }
        $input = $request->all();
        foreach($input as $key => $value){
            if(!in_array($key, config('constant.ignore_var'))){
                $config = \App\Config::firstOrNew(['name' => $key]);
                if($value != config('config.hidden_value'))
                $config->value = $value;
                $config->save();
            }
        }

        $this->logActivity(['module' => 'mail_configuration','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.mail').' '.trans('messages.configuration').' '.trans('messages.updated'), 'status' => 'success']; 
            $response = $this->getSetupGuide($response,'mail');
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/configuration#'.$config_type)->withSuccess(trans('messages.mail').' '.trans('messages.configuration').' '.trans('messages.updated'));         
    }

    public function sms(Request $request){

        $validation = Validator::make($request->all(),[
                'nextmo_api_key' => 'required',
                'nextmo_api_secret' => 'required',
                'nextmo_from_number' => 'required',
                ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $input = $request->all();
        foreach($input as $key => $value){
            if(!in_array($key, config('constant.ignore_var'))){
                $config = \App\Config::firstOrNew(['name' => $key]);
                if($value != config('config.hidden_value'))
                $config->value = $value;
                $config->save();
            }
        }
        $this->logActivity(['module' => 'sms_configuration','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sms').' '.trans('messages.configuration').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/configuration#'.$config_type)->withSuccess(trans('messages.sms').' '.trans('messages.configuration').' '.trans('messages.updated'));         
    }

    public function logo(Request $request){

        $validation = Validator::make($request->all(),[
            'logo' => 'image'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }

            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $filename = uniqid();
        $config = \App\Config::firstOrNew(['name' => 'logo']);

        if ($request->hasFile('logo') && $request->input('remove_logo') != 1){
            if(File::exists(config('constant.upload_path.logo').config('config.logo')))
                File::delete(config('constant.upload_path.logo').config('config.logo'));
            $extension = $request->file('logo')->getClientOriginalExtension();
            $file = $request->file('logo')->move(config('constant.upload_path.logo'), $filename.".".$extension);
            $img = Image::make(config('constant.upload_path.logo').$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(config('constant.upload_path.logo').$filename.".".$extension);
            $config->value = $filename.".".$extension;
        } elseif($request->input('remove_logo') == 1){
            if(File::exists(config('constant.upload_path.logo').config('config.logo')))
                File::delete(config('constant.upload_path.logo').config('config.logo'));
            $config->value = null;
        }

        $config->save();

        $this->logActivity(['module' => 'logo','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.configuration').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/configuration')->withSuccess(trans('messages.configuration').' '.trans('messages.updated'));
    }
    
    public function menu(Request $request){

        $data = $request->all();
        foreach(\App\Menu::all() as $menu_item){
            $menu_item->order = $request->input($menu_item->name);
            $menu_item->visible = $request->has($menu_item->name.'-visible') ? 1 : 0;
            $menu_item->save();
        }

        $config_type = $request->input('config_type');
        
        $this->logActivity(['module' => 'menu','activity' => 'activity_updated']);

        $response = ['status' => 'success','message' => trans('messages.menu').' '.trans('messages.configuration').' '.trans('messages.updated')];
        $response = $this->getSetupGuide($response,'menu');
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
    }

    public function setupGuide(Request $request){

        $setup = \App\Setup::orderBy('id','asc')->get();
        $setup_total = 0;
        $setup_completed = 0;
        foreach($setup as $value){
            $setup_total += config('setup.'.$value->module.'.weightage');
            if($value->completed)
                $setup_completed += config('setup.'.$value->module.'.weightage');
        }
        $setup_percentage = ($setup_total) ? round(($setup_completed/$setup_total) * 100) : 0;

        if($setup_percentage != 100 && !config('config.setup_guide')){
            if($request->has('ajax_submit')){
                $response = ['message' => 'We are back!', 'status' => 'success']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/home');
        }

        $config = \App\Config::firstOrNew(['name' => 'setup_guide']);
        $config->value = 0;
        $config->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.setup_guide_hide'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/home');
    }
}
