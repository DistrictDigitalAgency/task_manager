<?php
namespace App\Http\Controllers;
use App\TaskComment;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCommentRequest;

Class TaskCommentController extends Controller{
    use BasicController;

	public function store(TaskCommentRequest $request, $id){

		$task_comment = new TaskComment;
	    $task_comment->fill($request->all());
	    $task_comment->comment = clean($request->input('comment'));
	    $task_comment->task_id = $id;
	    $task_comment->user_id = \Auth::user()->id;
	    $task_comment->save();
		$this->logActivity(['module' => 'task','secondary_module' => 'comment','unique_id' => $id,'secondary_id' => $task_comment->id,'activity' => 'activity_added']);
	    
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.comment').' '.trans('messages.posted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/task/'.$id."#comment-tab")->withSuccess(trans('messages.comment').' '.trans('messages.posted'));
	}

	public function destroy($id,Request $request){

		$task_comment = TaskComment::find($id);

		if($task_comment->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'task','secondary_module' => 'comment','secondary_id' => $task_comment->id,'unique_id' => $task_comment->Task->id, 'activity' => 'activity_deleted']);
		$id = $task_comment->Task->id;
		$task_comment->delete();
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.comment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id.'#comment-tab')->withSuccess(trans('messages.comment').' '.trans('messages.deleted'));
	}
}
?>