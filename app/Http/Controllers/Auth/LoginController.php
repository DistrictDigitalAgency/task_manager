<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function credentials(Request $request)
    {
        if(config('config.login_type') == 'email')
            $field = 'email';
        elseif(config('config.login_type') == 'username')
            $field = 'username';
        else {
            $field = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $request->merge([$field => $request->input('email')]);
        }
        return $request->only($field, 'password');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        if($request->has('ajax_submit')){
            $response = ['message' => trans('auth.failed'), 'status' => 'error']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
    }

}
