<?php
namespace App\Http\Controllers;
use App\SubTask;
use Illuminate\Http\Request;
use Validator;

Class SubTaskController extends Controller{
    use BasicController;

    public function store($id,Request $request){
        $input = array('task_id' => $id);
        $request->merge($input);

        $message = array('title.unique_with' => trans('messages.sub_task_title_already_in_use'));

        $validation = Validator::make($request->all(),[
            'title' => 'required|unique_with:sub_tasks,task_id',
        ],$message);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $sub_task = new SubTask;
        $sub_task->task_id = $request->input('task_id');
        $sub_task->title = $request->input('title');
        $sub_task->description = $request->input('description');
        $sub_task->user_id = \Auth::user()->id;
        $sub_task->save();

        $this->logActivity(['module' => 'task','secondary_module' => 'sub_task','unique_id' => $sub_task->task_id,'secondary_id' => $sub_task->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub').' '.trans('messages.task').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withInput()->withSuccess(trans('messages.sub').' '.trans('messages.task').' '.trans('messages.added'));
    }

    public function lists(Request $request){
        $task = \App\Task::find($request->input('task_id'));
        return view('task.sub_task_list',compact('task'))->render();
    }

    public function edit($id){
    	$sub_task = SubTask::find($id);
    	if(!$sub_task || $sub_task->user_id != \Auth::user()->id)
            return view('global.error',['message' => trans('messages.permission_denied')]);

        return view('task.edit_sub_task',compact('sub_task'));
    }

    public function update($id,Request $request){
    	$sub_task = SubTask::find($id);

    	if(!$sub_task || $sub_task->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.permission_denied'));
		}

        $input = array('task_id' => $sub_task->task_id);
        $request->merge($input);

        $message = array('title.unique_with' => trans('messages.sub_task_title_already_in_use'));

        $validation = Validator::make($request->all(),[
            'title' => 'required|unique_with:sub_tasks,task_id,'.$sub_task->id,
        ],$message);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $sub_task->title = $request->input('title');
        $sub_task->description = $request->input('description');
        $sub_task->save();

        $this->logActivity(['module' => 'task','secondary_module' => 'sub_task','unique_id' => $sub_task->task_id,'secondary_id' => $sub_task->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub').' '.trans('messages.task').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.sub').' '.trans('messages.task').' '.trans('messages.updated'));
    }

    public function destroy($id,Request $request){
    	$sub_task = SubTask::find($id);

    	if(!$sub_task || $sub_task->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.permission_denied'));
		}

        $this->logActivity(['module' => 'task','secondary_module' => 'sub_task','unique_id' => $sub_task->task_id,'secondary_id' => $sub_task->id,'activity' => 'activity_deleted']);

		$sub_task->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub').' '.trans('messages.task').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.sub').' '.trans('messages.task').' '.trans('messages.deleted'));
    }
}