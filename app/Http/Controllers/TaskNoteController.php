<?php
namespace App\Http\Controllers;
use App\TaskNote;
use Illuminate\Http\Request;

Class TaskNoteController extends Controller{
    use BasicController;

	public function store(Request $request,$id){

		$note = TaskNote::firstOrNew(['task_id' => $id,'user_id' => \Auth::user()->id]);
		$note->note = $request->input('note');
	    $note->save();
		$this->logActivity(['module' => 'task','secondary_module' => 'note','unique_id' => $id,'secondary_id' => $note->id, 'activity' => 'activity_saved']);
	    
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.note').' '.trans('messages.saved'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/task/'.$id."#note-tab")->withSuccess(trans('messages.note').' '.trans('messages.saved'));
	}
}
?>