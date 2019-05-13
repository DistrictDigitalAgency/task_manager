<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\TaskPriorityRequest;
use App\TaskPriority;

Class TaskPriorityController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('task_priority.create');
	}

	public function lists(){
		$task_priorities = TaskPriority::all();
		return view('task_priority.list',compact('task_priorities'))->render();
	}

	public function edit(TaskPriority $task_priority){
		return view('task_priority.edit',compact('task_priority'));
	}

	public function store(TaskPriorityRequest $request, TaskPriority $task_priority){	

		$task_priority->fill($request->all())->save();

		$this->logActivity(['module' => 'task_priority','unique_id' => $task_priority->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $task_priority->name,'id' => $task_priority->id,'field' => 'task_priority_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.task').' '.trans('messages.priority').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.priority').' '.trans('messages.added'));				
	}

	public function update(TaskPriorityRequest $request, TaskPriority $task_priority){

		$task_priority->fill($request->all())->save();

		$this->logActivity(['module' => 'task_priority','unique_id' => $task_priority->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.task').' '.trans('messages.priority').' '.trans('messages.updated'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.priority').' '.trans('messages.updated'));
	}

	public function destroy(TaskPriority $task_priority,Request $request){

		$this->logActivity(['module' => 'task_priority','unique_id' => $task_priority->id,'activity' => 'activity_deleted']);

        $task_priority->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.task').' '.trans('messages.priority').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.priority').' '.trans('messages.deleted'));
	}
}
?>