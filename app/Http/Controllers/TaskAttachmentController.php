<?php
namespace App\Http\Controllers;
use App\TaskAttachment;
use File;
use Illuminate\Http\Request;
use App\Http\Requests\TaskAttachmentRequest;

Class TaskAttachmentController extends Controller{
    use BasicController;

	public function store(TaskAttachmentRequest $request,$id){

		$filename = uniqid();

		$task_attachment = new TaskAttachment;
     	if ($request->hasFile('attachments')) {
	 		$extension = $request->file('attachments')->getClientOriginalExtension();
	 		$file = $request->file('attachments')->move(config('constant.upload_path.attachments'), $filename.".".$extension);
	 		$task_attachment->attachments = $filename.".".$extension;
		 }

		$task_attachment->title = $request->input('title');
		$task_attachment->description = $request->input('description');
		$task_attachment->user_id = \Auth::user()->id;
		$task_attachment->task_id = $id;
		$this->logActivity(['module' => 'task','secondary_module' => 'attachment','unique_id' => $id,'secondary_id' => $task_attachment->id, 'activity' => 'activity_added']);

		$task_attachment->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attachment').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id."#attachment-tab")->withSuccess(trans('messages.attachment').' '.trans('messages.added'));
	}

	public function lists(Request $request){
        $task = \App\Task::find($request->input('task_id'));
        return view('task.attachment_list',compact('task'))->render();
	}

	public function download($id){
		$task_attachment = TaskAttachment::find($id);

		if(!$this->taskAccessible($task_attachment->task_id))
			return redirect('/task')->withErrors(trans('messages.permission_denied'));

		$file = config('constant.upload_path.attachments').$task_attachment->attachments;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect('/task')->withErrors(trans('messages.file_not_found'));
	}

	public function destroy($id,Request $request){

		$task_attachment = TaskAttachment::find($id);

		if($task_attachment->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'task','secondary_module' => 'attachment','unique_id' => $task_attachment->Task->id,'secondary_id' => $task_attachment->id, 'activity' => 'activity_deleted']);
		$id = $task_attachment->Task->id;
		File::delete(config('constant.upload_path.attachments').$task_attachment->attachments);
		$task_attachment->delete();
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attachment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id.'#attachment-tab')->withSuccess(trans('messages.attachment').' '.trans('messages.deleted'));
	}
}
?>