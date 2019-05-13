<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCategoryRequest;
use App\TaskCategory;

Class TaskCategoryController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('task_category.create');
	}

	public function lists(){
		$task_categories = TaskCategory::all();
		return view('task_category.list',compact('task_categories'))->render();
	}

	public function edit(TaskCategory $task_category){
		return view('task_category.edit',compact('task_category'));
	}

	public function store(TaskCategoryRequest $request, TaskCategory $task_category){	

		$task_category->fill($request->all())->save();

		$this->logActivity(['module' => 'task_category','unique_id' => $task_category->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $task_category->name,'id' => $task_category->id,'field' => 'task_category_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.task').' '.trans('messages.category').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.category').' '.trans('messages.added'));				
	}

	public function update(TaskCategoryRequest $request, TaskCategory $task_category){

		$task_category->fill($request->all())->save();

		$this->logActivity(['module' => 'task_category','unique_id' => $task_category->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.task').' '.trans('messages.category').' '.trans('messages.updated'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.category').' '.trans('messages.updated'));
	}

	public function destroy(TaskCategory $task_category,Request $request){

		$this->logActivity(['module' => 'task_category','unique_id' => $task_category->id,'activity' => 'activity_deleted']);

        $task_category->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.task').' '.trans('messages.category').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#task-tab')->withSuccess(trans('messages.task').' '.trans('messages.category').' '.trans('messages.deleted'));
	}
}
?>