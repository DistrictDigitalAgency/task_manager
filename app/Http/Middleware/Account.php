<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class Account
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $profile = Auth::user()->Profile;

        if(!isset($profile) && $profile == '' && $profile == null){
            $profile = new \App\Profile;
            $profile->user()->associate(Auth::user());
            $profile->save();
        }

        if(Auth::user()->status == 'pending_approval')
            $error_message = trans('messages.account_not_approved');
        elseif(Auth::user()->status == 'pending_activation')
            $error_message = trans('messages.account_not_activated');
        elseif(Auth::user()->status == 'banned')
            $error_message = trans('messages.account_banned');
        elseif(config('config.maintenance_mode') && !defaultRole() && Auth::check() && !$request->is('under-maintenance'))
            $error_message = trans('messages.under_maintenance_message');
        elseif(getMode() && config('config.enable_ip_filter') && \App\IpFilter::count() && !validateIp() && Auth::check() && defaultRole())
            $error_message = trans('messages.ip_not_allowed');

        if(isset($error_message)){
            Auth::logout();
            $response = ['message' => $error_message, 'status' => 'error'];
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        
        if(config('config.enable_two_factor_auth') && session()->has('two_factor_auth') && !$request->is('verify-security')){
            $response = ['message' => config('config.redirecting_message'), 'status' => 'success','redirect' => '/verify-security' ];
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return $next($request);
    }
}
