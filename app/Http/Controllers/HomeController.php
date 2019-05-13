<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class HomeController extends Controller
{
    use BasicController;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = \App\Announcement::with('designation')
                ->where('from_date','<=',date('Y-m-d'))
                ->where('to_date','>=',date('Y-m-d'))
                ->where(function($q){
                    $q->whereHas('designation',function($query) {
                        $query->where('designation_id','=',\Auth::user()->designation_id);
                    })->orWhere(function ($query) { 
                        $query->doesntHave('designation'); 
                    })->orWhere('user_id','=',\Auth::user()->id);
                })->get();

        $all_birthdays = \App\Profile::whereBetween( \DB::raw('dayofyear(date_of_birth) - dayofyear(curdate())'), [0,config('config.celebration_days')])
            ->orWhereBetween( \DB::raw('dayofyear(curdate()) - dayofyear(date_of_birth)'), [0,config('config.celebration_days')])
            ->orderBy('date_of_birth','asc')
            ->get();

        $all_anniversaries = \App\Profile::whereBetween( \DB::raw('dayofyear(date_of_anniversary) - dayofyear(curdate())'), [0,config('config.celebration_days')])
            ->orWhereBetween( \DB::raw('dayofyear(curdate()) - dayofyear(date_of_anniversary)'), [0,config('config.celebration_days')])
            ->orderBy('date_of_anniversary','asc')
            ->get();

        $celebrations = array();
        foreach($all_birthdays as $all_birthday){
            $number = date('Y') - date('Y',strtotime($all_birthday->date_of_birth));
            $celebrations[strtotime(date('d M',strtotime($all_birthday->date_of_birth)))] = array(
                'icon' => 'birthday-cake',
                'title' => getDateDiff($all_birthday->date_of_birth) ? : date('d M',strtotime($all_birthday->date_of_birth)),
                'date' => $all_birthday->date_of_birth,
                'number' => $number.'<sup>'.daySuffix($number).'</sup>'.' '.trans('messages.birthday'),
                'id' => $all_birthday->User->id,
                'name' => $all_birthday->User->full_name
            );
        }
        foreach($all_anniversaries as $all_anniversary){
            $number = date('Y') - date('Y',strtotime($all_anniversary->date_of_anniversary));
            $celebrations[strtotime(date('d M',strtotime($all_anniversary->date_of_anniversary)))] = array(
                'icon' => 'gift',
                'title' => getDateDiff($all_anniversary->date_of_anniversary) ? : date('d M',strtotime($all_anniversary->date_of_anniversary)),
                'date' => $all_anniversary->date_of_anniversary,
                'number' => $number.'<sup>'.daySuffix($number).'</sup>'.' '.trans('messages.anniversary'),
                'id' => $all_anniversary->User->id,
                'name' => $all_anniversary->User->full_name
            );
        }

        ksort($celebrations);

        $birthdays = \App\Profile::whereNotNull('date_of_birth')->orderBy('date_of_birth','asc')->get();

        $anniversaries = \App\Profile::whereNotNull('date_of_anniversary')->orderBy('date_of_anniversary','asc')->get();

        $todos = \App\Todo::where('user_id','=',\Auth::user()->id)
            ->orWhere(function ($query)  {
                $query->where('user_id','!=',\Auth::user()->id)
                    ->where('visibility','=','public');
            })->get();

        $events = array();
        foreach($birthdays as $birthday){
            $start = date('Y').'-'.date('m-d',strtotime($birthday->date_of_birth));
            $title = trans('messages.birthday').' : '.$birthday->User->full_name;
            $color = '#133edb';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color);
        }
        foreach($anniversaries as $anniversary){
            $start = date('Y').'-'.date('m-d',strtotime($anniversary->date_of_anniversary));
            $title = trans('messages.anniversary').' : '.$anniversary->User->full_name;
            $color = '#133edb';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color);
        }
        foreach($todos as $todo){
            $start = $todo->date;
            $title = trans('messages.to_do').' : '.$todo->title.' '.$todo->description;
            $color = '#ff0000';
            $url = '/todo/'.$todo->id.'/edit';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color, 'url' => $url);
        }

        $calendar_tasks = \App\Task::whereHas('user',function($query){
            $query->where('user_id',\Auth::user()->id);
        })->orWhere('user_id',\Auth::user()->id)->get();

        foreach($calendar_tasks as $calendar_task){
            $events[] = array(
                'title' => trans('messages.task').' '.trans('messages.start').' '.trans('messages.date').' : '.$calendar_task->title, 
                'start' => $calendar_task->start_date, 
                'color' => '#50f442', 
                'url' => '/task/'.$calendar_task->id);

            $events[] = array(
                'title' => trans('messages.task').' '.trans('messages.due').' '.trans('messages.date').' : '.$calendar_task->title, 
                'start' => $calendar_task->due_date, 
                'color' => '#f44242', 
                'url' => '/task/'.$calendar_task->id);
        }

        $child_designation = childDesignation(\Auth::user()->Profile->designation_id,1);
        $child_staff_count = \App\User::with('profile')->whereHas('profile',function($query) use($child_designation){
            $query->whereIn('designation_id',$child_designation);
        })->count();

        $tree = array();
        $designations = \App\Designation::all();
        foreach ($designations as $designation){
            $tree[$designation->id] = array(
                'parent_id' => $designation->top_designation_id,
                'name' => $designation->full_designation
            );
        }

        $query = $this->fetchTask();
        $tasks = $query->get();

        $assets = ['calendar'];
        $menu = 'home';
        return view('home',compact('assets','events','birthdays','celebrations','menu','announcements','child_staff_count','tree','tasks'));
    }

    public function sidebar(Request $request){
        $menu = explode(',',$request->input('menu'));
        return view('layouts.menu',compact('menu'));
    }

    public function activityLog(){
        $table_data['activity-log-table'] = array(
            'source' => 'activity-log',
            'title' => 'Activity Log List',
            'id' => 'activity_log_table',
            'disable-sorting' => 1,
            'data' => array(
                'S No',
                trans('messages.user'),
                trans('messages.activity'),
                'IP',
                trans('messages.date'),
                'User Agent',
                ),
            'form' => 'activity-log-filter-form'
            );

        $query = getAccessibleUser();
        $users = $query->get()->pluck('full_name_with_designation','id')->all();

        $assets = ['datatable'];
        return view('activity_log.index',compact('table_data','assets','users'));
    }

    public function activityLogList(Request $request){

        $qry = getAccessibleUser();
        $users = $qry->get()->pluck('id')->all();

        $query = \App\Activity::whereIn('user_id',$users);

        if($request->has('user_id'))
            $query->where('user_id','=',$request->input('user_id'));

        if($request->has('start_date') && $request->has('end_date'))
            $query->whereBetween('created_at',[$request->input('start_date').' 00:00:00',$request->input('end_date').' 23:59:59']);

        $activities = $query->orderBy('created_at','desc')->get();

        $rows = array();
        $i = 0;
        foreach($activities as $activity){
            $i++;

            $activity_detail = ($activity->activity == 'activity_added') ? trans('messages.new').' '.trans('messages.'.$activity->module).' '.trans('messages.'.$activity->activity) : trans('messages.'.$activity->module).' '.trans('messages.'.$activity->activity);
            $row = array(
                $i,
                $activity->User->full_name,
                $activity_detail,
                $activity->ip,
                showDateTime($activity->created_at),
                $activity->user_agent
                );

            $rows[] = $row;
        }

        $list['aaData'] = $rows;
        return json_encode($list);
    }

    public function lock(){
        if(session('locked'))
            return view('auth.lock');
        else
            return redirect('/home');
    }

    public function unlock(Request $request){
        if(!\Auth::check()){
            $response = ['message' => trans('messages.session_expire'), 'status' => 'success'];
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        $validation = Validator::make($request->all(),[
            'password' => 'required'
        ]);

        if($validation->fails()){
            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        $password = $request->input('password');

        if(\Hash::check($password,\Auth::user()->password)){
            session()->forget('locked');
            $response = ['message' => '', 'status' => 'success'];
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        $response = ['message' => trans('messages.unlock_failed'), 'status' => 'error'];
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
    }

    public function filter(Request $request){
        $response = ['message' => trans('messages.request').' '.trans('messages.submitted'), 'status' => 'success'];
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
    }
}
