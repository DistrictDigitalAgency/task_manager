<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Entrust;

trait BasicController {

    public function logActivity($data) {
    	$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : ((\Auth::check()) ? \Auth::user()->id : null);
    	$data['ip'] = \Request::getClientIp();
        $data['secondary_id'] = isset($data['secondary_id']) ? $data['secondary_id'] : null;
        $data['user_agent'] = \Request::header('User-Agent');
        if(config('config.enable_activity_log'))
    	$activity = \App\Activity::create($data);
    }
    
    public function logEmail($data){
        $data['to_address'] = $data['to'];
        unset($data['to']);
        $data['from_address'] = config('mail.from.address');
        if(config('config.enable_email_log'))
        \App\Email::create($data);
    }

    public function getSetupGuide($response, $menu = null){
        if($menu && \App\Setup::whereModule($menu)->whereCompleted(0)->first())
            \App\Setup::whereModule($menu)->whereCompleted(0)->update(['completed' => 1]);

        if(config('config.setup_guide') && defaultRole()){
            $setup_guide = setupGuide();
            $response['setup_guide'] = $setup_guide;
        }
        return $response;
    }

    public function designationAccessible($designation){
        if(Entrust::can('manage-all-designation') || (Entrust::can('manage-subordinate-designation') && isChild($designation->id)))
            return 1;
        else
            return 0;
    }

    public function AnnouncementAccessible($announcement){
        if(Entrust::can('manage-all-announcement') || (Entrust::can('manage-subordinate-announcement') && (isChild($announcement->User->Profile->designation_id) || $announcement->user_id == \Auth::user()->id)))
            return 1;
        else
            return 0;
    }

    public function userAccessible($user_id){
        if(in_array($user_id, getAccessibleUserList()))
            return 1;
        else
            return 0;
    }

    public function fetchTask(){
        if(Entrust::can('manage-all-task'))
            $query = \App\Task::whereNotNull('id');
        elseif(Entrust::can('manage-subordinate-task')){
            $user_query = getAccessibleUser(\Auth::user()->id,1);
            $subordinate_users = $user_query->get()->pluck('id')->all();
            $query = \App\Task::where(function($q) use($subordinate_users){
                $q->whereHas('user', function($q1) use($subordinate_users){
                    $q1->whereIn('user_id',$subordinate_users);
                })->orWhere('user_id','=',\Auth::user()->id);
            });
        } else 
            $query = \App\Task::where(function($q){
                $q->whereHas('user', function($q1){
                    $q1->where('user_id',\Auth::user()->id);
                })->orWhere('user_id','=',\Auth::user()->id);
            });

        return $query;
    }

    public function taskAccessible($task_id){

        $query = $this->fetchTask();

        $tasks = $query->get()->pluck('id')->all();

        if(in_array($task_id, $tasks))
            return 1;
        else
            return 0;
    }
}