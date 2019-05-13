<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Task;
use Entrust;
use Validator;

Class TaskController extends Controller{
    use BasicController;

    protected $form = 'task-form';

	public function index(){

		if(!Entrust::can('list-task'))
			return redirect('/home')->withErrors(trans('messages.permission_denied'));

		$data = array(
	        		trans('messages.option'),
	        		trans('messages.title'),
	        		trans('messages.status'),
	        		trans('messages.category'),
	        		trans('messages.priority'),
	        		trans('messages.progress'),
	        		trans('messages.start').' '.trans('messages.date'),
	        		trans('messages.due').' '.trans('messages.date'),
	        		trans('messages.complete').' '.trans('messages.date'),
	        		trans('messages.user')
        		);

		$data = putCustomHeads($this->form, $data);

		$table_data['task-table'] = array(
				'source' => 'task',
				'title' => 'Task List',
				'id' => 'task_table',
				'data' => $data,
				'form' => 'task-filter-form'
			);

		$task_categories = \App\TaskCategory::all()->pluck('name','id')->all();
		$task_priorities = \App\TaskPriority::all()->pluck('name','id')->all();

        $query = getAccessibleUser();
        $users = $query->get()->pluck('full_name_with_designation','id')->all();

		$assets = ['datatable','summernote','tags','slider'];
		$menu = 'task';
		return view('task.index',compact('table_data','assets','menu','task_categories','task_priorities','users'));
	}

	public function lists(Request $request){
		if(!Entrust::can('list-task'))
			return redirect('/home')->withErrors(trans('messages.permission_denied'));

		$query = $this->fetchTask();

        if($request->has('task_category_id'))
            $query->whereIn('task_category_id',$request->input('task_category_id'));

        if($request->has('task_priority_id'))
            $query->whereIn('task_priority_id',$request->input('task_priority_id'));

        if($request->has('progress'))
        	$query->whereBetween('progress',explode(',',$request->input('progress')));

        if($request->has('user_id'))
        	$query->whereHas('user',function($q) use($request){
        		$q->whereIn('user_id',$request->input('user_id'));
        	});

        if($request->has('type') && $request->input('type') == 'owned')
        	$query->where('user_id',\Auth::user()->id);
        elseif($request->has('type') && $request->input('type') == 'assigned')
        	$query->whereHas('user',function($q){
        		$q->where('user_id',\Auth::user()->id);
        	});

        if($request->has('start_date_start') && $request->has('start_date_end'))
        	$query->whereBetween('start_date',[$request->input('start_date_start'),$request->input('start_date_end')]);

        if($request->has('due_date_start') && $request->has('due_date_end'))
        	$query->whereBetween('due_date',[$request->input('due_date_start'),$request->input('due_date_end')]);

        if($request->has('complete_date_start') && $request->has('complete_date_end'))
        	$query->whereBetween('complete_date',[$request->input('complete_date_start'),$request->input('complete_date_end')]);

        if($request->has('status')){
        	if($request->input('status') == 'unassigned')
        		$query->doesntHave('user');
        	elseif($request->input('status') == 'pending')
        		$query->whereBetween('progress',[0,99])->where('due_date','>',date('Y-m-d'));
        	elseif($request->input('status') == 'complete')
        		$query->where('progress','=',100);
        	elseif($request->input('status') == 'overdue')
        		$query->where('progress','<',100)->where('due_date','<',date('Y-m-d'));
        }

		$tasks = $query->get();

        $col_ids = getCustomColId($this->form);
        $values = fetchCustomValues($this->form);
        $rows = array();

        foreach($tasks as $task){

        	$progress = $task->progress.'% <div class="progress progress-xs" style="margin-top:0px;">
						  <div class="progress-bar progress-bar-'.progressColor($task->progress).'" role="progressbar" aria-valuenow="'.$task->progress.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$task->progress.'%">
						  </div>
						</div>';

			$status = getTaskStatus($task);

        	$user_list = '<ol>';
        	foreach($task->user as $user)
        		$user_list .= '<li>'.$user->full_name.'</li>';
        	$user_list .= '</ol>';

			$row = array(
				'<div class="btn-group btn-group-xs">'.
				'<a href="/task/'.$task->id.'" class="btn btn-xs btn-default"> <i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="'.trans('messages.view').'"></i></a>'.
				(($task->StarredTask->where('user_id',\Auth::user()->id)->count()) ? 
					('<a href="#" data-ajax="1" data-extra="&task_id='.$task->id.'" data-source="/task-starred" class="btn btn-xs btn-default"> <i class="fa fa-star starred" data-toggle="tooltip" title="'.trans('messages.remove').' '.trans('messages.favourite').'"></i></a>') : ('<a href="#" data-ajax="1" data-extra="&task_id='.$task->id.'" data-source="/task-starred" class="btn btn-xs btn-default"> <i class="fa fa-star-o" data-toggle="tooltip" title="'.trans('messages.mark').' '.trans('messages.as').' '.trans('messages.favourite').'"></i></a>')).
				(Entrust::can('edit-task') ? '<a href="#" data-href="/task/'.$task->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				(Entrust::can('delete-task') ? delete_form(['task.destroy',$task->id]) : '').
				'</div>',
				$task->title,
				$status,
				$task->TaskCategory->name,
				$task->TaskPriority->name,
				$progress,
				showDate($task->start_date),
				showDate($task->due_date),
				showDateTime($task->complete_date),
				$user_list,
				);
			$id = $task->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
			$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function fetch(Request $request){

		$query = $this->fetchTask();

		if($request->input('type') == 'starred')
			$query->whereHas('starredTask',function($q) use($request){
				$q->where('user_id','=',\Auth::user()->id);
			})->orderBy('start_date','desc');
		elseif($request->input('type') == 'owned')
			$query->where('user_id','=',\Auth::user()->id)->orderBy('start_date','desc');
		elseif($request->input('type') == 'overdue')
			$query->where('progress','<',100)->where('due_date','<',date('Y-m-d'))->orderBy('due_date','asc');
		elseif($request->input('type') == 'pending')
			$query->where('progress','<',100)->orderBy('due_date','asc');
		elseif($request->input('type') == 'unassigned')
			$query->doesntHave('user')->orderBy('start_date','desc');

		$tasks = $query->take(5)->get();

		$type = $request->input('type');

		return view('task.fetch',compact('tasks','type'))->render();
	}

	public function show(Task $task){
		if(!$this->taskAccessible($task->id))
			return redirect('/task')->withErrors(trans('messages.permission_denied'));

		$assets = ['summernote','tags','slider'];
		return view('task.show',compact('task','assets'));
	}

	public function detail(Request $request){
		$task = Task::find($request->input('id'));

		$status = getTaskStatus($task,'lb-md');

		return view('task.detail',compact('task','status'))->render();
	}

	public function starred(Request $request){
		if(!$this->taskAccessible($request->input('task_id'))){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.permission_denied'));
		}

		$task = Task::find($request->input('task_id'));

		if($task->StarredTask->where('user_id',\Auth::user()->id)->count())
			\App\StarredTask::whereTaskId($task->id)->whereUserId(\Auth::user()->id)->delete();
		else
			\App\StarredTask::create(['task_id' => $task->id,'user_id' => \Auth::user()->id]);
			
		if($request->has('ajax_submit')){
            $response = ['status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task');
	}

	public function description(Request $request){
		$task = Task::find($request->input('id'));

		return view('task.description',compact('task'));
		return $task->description;
	}

	public function activity(Request $request){
		$task = Task::find($request->input('id'));

		$activities = \App\Activity::whereModule('task')->whereUniqueId($task->id)->orderBy('created_at','desc')->get();

		return view('task.activity',compact('task','activities'))->render();
	}

	public function comment(Request $request){
		$task = Task::find($request->input('id'));
		return view('task.comment',compact('task'))->render();
	}

	public function progress($id, Request $request){
		if(!$this->taskAccessible($id)){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/home')->withErrors(trans('messages.permission_denied'));
		}

		$task = Task::find($id);

		$task->progress = $request->input('progress');
		if($request->input('progress') == '100')
			$task->complete_date = new \DateTime;
		else
			$task->complete_date = null;
		$task->save();

		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_progress_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.progress').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.task').' '.trans('messages.progress').' '.trans('messages.updated'));	
	}

	public function edit(Task $task){
		if($task->user_id != \Auth::user()->id || !Entrust::can('edit-task') )
            return view('global.error',['message' => trans('messages.permission_denied')]);

		$task_categories = \App\TaskCategory::all()->pluck('name','id')->all();
		$task_priorities = \App\TaskPriority::all()->pluck('name','id')->all();

        $query = getAccessibleUser();
        $users = $query->get()->pluck('full_name_with_designation','id')->all();

		$selected_user = array();

		foreach($task->User as $user)
			$selected_user[] = $user->id;

        return view('task.edit',compact('task','task_categories','task_priorities','users','selected_user'));
	}

	public function store(TaskRequest $request, Task $task){
		if(!Entrust::can('create-task')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/home')->withErrors(trans('messages.permission_denied'));
		}
	
        $validation = validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$data = $request->all();
	    $task->fill($data);
	    $task->description = clean($request->input('description'));
	    $task->user_id = \Auth::user()->id;
		$task->save();
	    $task->user()->sync(($request->input('user_id')) ? : []);
		storeCustomField($this->form,$task->id, $data);
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_added']);

		if($request->input('task_assign_email') && $task->User->count()){
			$template = \App\Template::whereSlug('task-assign-email')->first();
			$body = ($template) ? $template->body : '';
			$subject = ($template) ? $template->subject : '';
			foreach($task->User as $user){

				$body = $this->emailTemplate($body,$task,$user);
	            $mail['subject'] = $subject;
	            $mail['email'] = $user->email;

		        \Mail::queue('emails.email', compact('body'), function($message) use ($mail){
		            $message->to($mail['email'])->subject($mail['subject']);
		        });
		        $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));
			}
		}

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.task').' '.trans('messages.added'));	
	}

	public function emailTemplate($body,$task,$user){
		$body = str_replace('[NAME]',$user->full_name,$body);
		$body = str_replace('[USERNAME]',$user->username,$body);
		$body = str_replace('[EMAIL]',$user->email,$body);
		$body = str_replace('[DESIGNATION]',$user->Profile->Designation->name,$body);
		$body = str_replace('[DEPARTMENT]',$user->Profile->Designation->Department->name,$body);
		$body = str_replace('[TASK_TITLE]',$task->title,$body);
		$body = str_replace('[TASK_CATEGORY]',$task->TaskCategory->name,$body);
		$body = str_replace('[TASK_PRIORITY]',$task->TaskPriority->name,$body);
		$body = str_replace('[TASK_START_DATE]',showDate($task->start_date),$body);
		$body = str_replace('[TASK_DUE_DATE]',showDate($task->due_date),$body);
		$body = str_replace('[TASK_OWNER_NAME]',$task->UserAdded->full_name,$body);
		$body = str_replace('[TASK_OWNER_EMAIL]',$task->UserAdded->email,$body);
		$body = str_replace('[TASK_OWNER_DESIGNATION]',$task->UserAdded->Profile->Designation->name,$body);
		$body = str_replace('[TASK_OWNER_DEPARTMENT]',$task->UserAdded->Profile->Designation->Department->name,$body);
        $body = str_replace('[CURRENT_DATE_TIME]',showDateTime(date('Y-m-d H:i:s')),$body);
        $body = str_replace('[CURRENT_DATE]',showDate(date('Y-m-d')),$body);

        return $body;
	}

	public function emailContent(Request $request){
		$template = \App\Template::whereId($request->input('template_id'))->first();
		$body = ($template) ? $template->body : '';
		$subject = ($template) ? $template->subject : '';

		$task = Task::find($request->input('task_id'));
		$user = \App\User::find($request->input('user_id'));

		$body = $this->emailTemplate($body,$task,$user);

        $response = ['body' => $body, 'subject' => $subject,'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function userEmail($task_id,$user_id){
		$task = Task::find($task_id);

		if(!$task || $task->user_id != \Auth::user()->id)
            return view('global.error',['message' => trans('messages.permission_denied')]);

        $users = $task->User->pluck('id')->all();

        if(!in_array($user_id, $users))
            return view('global.error',['message' => trans('messages.permission_denied')]);

        $templates = \App\Template::whereCategory('task')->pluck('name','id')->all();

        return view('task.email',compact('task','templates','user_id'));
	}

	public function postUserEmail(Request $request, $task_id,$user_id){
		$task = Task::find($task_id);

		if(!$task || $task->user_id != \Auth::user()->id)
            return view('global.error',['message' => trans('messages.permission_denied')]);

        $users = $task->User->pluck('id')->all();

        if(!in_array($user_id, $users))
            return view('global.error',['message' => trans('messages.permission_denied')]);

        $user = \App\User::find($user_id);

    	$mail['email'] = $user->email;
        $mail['subject'] = $request->input('subject');
        $body = $request->input('body');

        \Mail::send('emails.email', compact('body'), function($message) use ($mail){
            $message->to($mail['email'])->subject($mail['subject']);
        });
        $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));

        $this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_mail_sent']);
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.mail').' '.trans('messages.sent'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.mail').' '.trans('messages.sent'));
	}

	public function update(TaskRequest $request, Task $task){
		if(!Entrust::can('edit-task') || $task->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/home')->withErrors(trans('messages.permission_denied'));
		}

        $validation = validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
		$data = $request->all();
		$task->fill($data);
	    $task->description = clean($request->input('description'));
		$task->save();
	    $task->user()->sync(($request->input('user_id')) ? : []);
		updateCustomField($this->form,$task->id, $data);
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task')->withSuccess(trans('messages.task').' '.trans('messages.updated'));
	}

	public function destroy(Task $task, Request $request){
		if(!Entrust::can('delete-task') || $task->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/home')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_deleted']);
		deleteCustomField($this->form, $task->id);
		$task->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/task')->withSuccess(trans('messages.task').' '.trans('messages.deleted'));
	}

	public function ratingType(Request $request){

		$task = Task::find($request->input('task_id'));

		if($task->user_id != \Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/home')->withErrors(trans('messages.permission_denied'));
		}

		$sub_task_rating = $request->input('sub_task_rating');

		$task->sub_task_rating = $sub_task_rating;
		$task->save();
		$this->logActivity(['module' => 'task','secondary_module' => 'rating', 'unique_id' => $task->id,'activity' => 'activity_updated']);

		if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.configuration').' '.trans('messages.saved'),'status' => 'success','redirect' => '/task/'.$task->id]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back();
	}

	public function rating($task_id,$user_id){
		$task = Task::find($task_id);

        $valid_user = Task::whereId($task_id)->whereHas('user',function($q) use($user_id){
        	$q->where('user_id',$user_id);
        })->count();

		if($task->sub_task_rating || $task->user_id != \Auth::user()->id || !$valid_user)
            return view('global.error',['message' => trans('messages.permission_denied')]);

        $user_rating = '';
        $user_comment = '';
        foreach($task->user as $user){
        	if($user->id == $user_id){
        		$user_rating = $user->pivot->rating;
        		$user_comment = $user->pivot->comment;
        	}
        }

        $user = \App\User::find($user_id);
		return view('task.rating',compact('task','user','user_rating','user_comment'));
	}

	public function listRating(Request $request){
		$task = Task::find($request->input('task_id'));

		if($task->sub_task_rating || !$task)
			return;

		return view('task.task_rating_list',compact('task'))->render();
	}

	public function storeRating(Request $request, $task_id,$user_id){
		$task = Task::find($task_id);

		if(!$task || $task->user_id != \Auth::user()->id){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.permission_denied'));
		}

		if(!$task->sub_task_rating) {
			$validation_rules['rating'] = 'required';

	        $validation = Validator::make($request->all(),$validation_rules);

	        if($validation->fails()){
	            if($request->has('ajax_submit')){
	                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	            }
	            return redirect()->back()->withErrors($validation->messages()->first());
	        }
		}

		if(!$task->sub_task_rating){
	        $task->user()->sync([$user_id => [
					'rating' => $request->input('rating'),
					'comment' => ($request->has('comment')) ? $request->input('comment') : null
				]], false); 
		} else {
			$rating = $request->input('rating');
			$comment = $request->input('comment');
			foreach($task->SubTask as $sub_task){
				$sub_task_rating = \App\SubTaskRating::firstOrNew(['sub_task_id' => $sub_task->id,'user_id' => $user_id]);
				$sub_task_rating->sub_task_id = $sub_task->id;
				$sub_task_rating->user_id = $user_id;
				$sub_task_rating->rating = $rating[$sub_task->id];
				$sub_task_rating->comment = $comment[$sub_task->id];
				$sub_task_rating->save();
			}
		}

		$this->logActivity(['module' => 'task','secondary_module' => 'rating', 'unique_id' => $task->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.rating').' '.trans('messages.saved'), 'status' => 'success','refresh_table' => ($task->sub_task_rating ? 'sub-task-rating-table' : 'task-rating-table') ,'refresh_content' => 'load-task-activity']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$task->id)->withSuccess(trans('messages.rating').' '.trans('messages.saved'));
	}

	public function destroyTaskRating(Request $request){
		$task = Task::find($request->input('task_id'));

		if($task->sub_task_rating || !$task || $task->user_id != \Auth::user()->id){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.permission_denied'));
		}

		$task->user()->sync([$request->input('user_id') => [
				'rating' => null,
				'comment' => null
			]], false); 

		$this->logActivity(['module' => 'task','secondary_module' => 'rating', 'unique_id' => $task->id,'activity' => 'activity_deleted']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.rating').' '.trans('messages.deleted'), 'status' => 'success','refresh_content' => 'load-task-activity']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$task->id)->withSuccess(trans('messages.rating').' '.trans('messages.deleted'));
	}

	public function subTaskRating($task_id,$user_id){
		$task = Task::find($task_id);
		$user = \App\User::find($user_id);

        $users = $task->user()->pluck('user_id')->all();

		if(!$task->sub_task_rating || !$task || !$user || $task->user_id != \Auth::user()->id || !in_array($user->id,$users))
            return view('global.error',['message' => trans('messages.permission_denied')]);

        if(!$task->SubTask->count())
            return view('global.error',['message' => trans('messages.no_sub_task_found')]);

        return view('task.sub_task_rating',compact('task','user'));
	}

	public function listSubTaskRating(Request $request){
		$task = Task::find($request->input('task_id'));

		if(!$task->sub_task_rating || !$task)
			return;

		return view('task.sub_task_rating_list',compact('task'))->render();
	}

	public function showSubTaskRating($task_id,$user_id){
		$task = \App\Task::find($task_id);
        $user = \App\User::find($user_id);

        $users = $task->user()->pluck('user_id')->all();

		if(!$task->sub_task_rating || !$task || !$user || $task->user_id != \Auth::user()->id || !in_array($user->id,$users))
            return view('global.error',['message' => trans('messages.permission_denied')]);

        return view('task.sub_task_rating_view',compact('task','user'));
	}

	public function destroySubTaskRating(Request $request){
		$task = Task::find($request->input('task_id'));

		if(!$task->sub_task_rating || !$task || $task->user_id != \Auth::user()->id){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.permission_denied'));
		}

		$sub_tasks = $task->SubTask->pluck('id')->all();

		$sub_task_rating = \App\SubTaskRating::whereIn('sub_task_id',$sub_tasks)->whereUserId($request->input('user_id'))->get();

		if(!$sub_task_rating->count()){
			if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.invalid_link'));
		}

		\App\SubTaskRating::whereIn('sub_task_id',$sub_tasks)->whereUserId($request->input('user_id'))->delete();

		$this->logActivity(['module' => 'task','secondary_module' => 'rating', 'unique_id' => $task->id,'activity' => 'activity_deleted']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.rating').' '.trans('messages.deleted'), 'status' => 'success','refresh_content' => 'load-task-activity']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$task->id)->withSuccess(trans('messages.rating').' '.trans('messages.deleted'));
	}

	public function userTaskRating(){

        $data = array(
        		trans('messages.user'),
        		trans('messages.total').' '.trans('messages.task'),
        		trans('messages.complete').' '.trans('messages.task'),
        		trans('messages.overdue').' '.trans('messages.task'),
        		trans('messages.rating')
        		);

        $menu = 'report,user_task_rating';
        $table_data['user-task-rating-table'] = array(
			'source' => 'user-task-rating',
			'title' => 'User Task Rating',
			'id' => 'user-task-rating-table',
			'form' => 'user-task-rating-form',
			'data' => $data
		);

        $designations = \App\Designation::whereIn('id',getDesignation(\Auth::user()))->get()->pluck('full_designation','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();
		$assets = ['datatable'];

		return view('task.user_task_rating',compact('table_data','menu','assets','designations','locations'));
	}

	public function userTaskRatingLists(Request $request){

        $rows=array();

        $query = getAccessibleUser();

        if($request->has('designation_id'))
            $query->whereHas('profile',function($q) use ($request){
                $q->whereIn('designation_id',$request->input('designation_id'));
            });

        $location = ($request->has('location_id')) ? \App\Location::whereIn('id',$request->input('location_id'))->get()->pluck('id')->all() : null;

        $users = $query->get();

		foreach($users as $user){

            $user_location = getLocation($user->id);

			$rating = 0;
			$completed_task = $user->Task->where('progress','100')->count();
			$overdue_task = $user->Task->filter(function($item){
					return (data_get($item, 'progress') < '100');
				})->filter(function($item) {
					return (data_get($item, 'due_date') < date('Y-m-d'));
				})->count();
			$total_task = $user->Task->count();
			foreach($user->Task as $task){
				if($task->sub_task_rating)
					$rating += getSubTaskRating($task->id,$user->id,1);
				else
					$rating += $task->pivot->rating;
			}

			$average_rating = ($total_task) ? $rating/$total_task : 0;

			if(!$location || ($user_location && in_array($user_location->location_id, $location)))
			$rows[] = array(
				$user->full_name_with_designation,
				$total_task,
				$completed_task,
				$overdue_task,
				getRatingStar($average_rating)
			);
		}
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function userTaskSummary(){

        $query = getAccessibleUser();

        $users = $query->get()->pluck('full_name_with_designation','id')->all();

        $data = array(
        		trans('messages.option'),
        		trans('messages.title'),
        		trans('messages.category'),
        		trans('messages.priority'),
        		trans('messages.start').' '.trans('messages.date'),
        		trans('messages.due').' '.trans('messages.date'),
        		trans('messages.complete').' '.trans('messages.date'),
        		trans('messages.progress'),
        		trans('messages.rating')
        		);

        $menu = 'report,user_task_summary';
        $table_data['user-task-summary-table'] = array(
			'source' => 'user-task-summary',
			'title' => 'User Task Summary',
			'id' => 'user-task-summary-table',
			'form' => 'user-task-summary-form',
			'data' => $data
		);

		$task_categories = \App\TaskCategory::all()->pluck('name','id')->all();
		$task_priorities = \App\TaskPriority::all()->pluck('name','id')->all();

		$assets = ['datatable','slider'];

		return view('task.user_task_summary',compact('table_data','menu','assets','users','task_categories','task_priorities'));
	}

	public function userTaskSummaryLists(Request $request){
		$query = Task::whereHas('user',function($q) use($request){
			$q->where('user_id',$request->input('user_id'));
		});

        if($request->has('task_category_id'))
            $query->whereIn('task_category_id',$request->input('task_category_id'));

        if($request->has('task_priority_id'))
            $query->whereIn('task_priority_id',$request->input('task_priority_id'));

        if($request->has('progress'))
        	$query->whereBetween('progress',explode(',',$request->input('progress')));


        if($request->has('start_date_start') && $request->has('start_date_end'))
        	$query->whereBetween('start_date',[$request->input('start_date_start'),$request->input('start_date_end')]);

        if($request->has('due_date_start') && $request->has('due_date_end'))
        	$query->whereBetween('due_date',[$request->input('due_date_start'),$request->input('due_date_end')]);

        if($request->has('complete_date_start') && $request->has('complete_date_end'))
        	$query->whereBetween('complete_date',[$request->input('complete_date_start'),$request->input('complete_date_end')]);

        $tasks = $query->get();

        $rows=array();

        foreach($tasks as $task){

        	$progress = $task->progress.'% <div class="progress progress-xs" style="margin-top:0px;">
						  <div class="progress-bar progress-bar-'.progressColor($task->progress).'" role="progressbar" aria-valuenow="'.$task->progress.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$task->progress.'%">
						  </div>
						</div>';

			if($task->sub_task_rating)
				$rating = getSubTaskRating($task->id,$request->input('user_id'),1);
			else
				$rating = $task->pivot->rating;

        	$rows[] = array(
        		'<div class="btn-group btn-group-xs">'.
					'<a href="/task/'.$task->id.'" class="btn btn-xs btn-default"> <i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="'.trans('messages.view').'"></i></a>
				</div>',
        		$task->title,
        		$task->TaskCategory->name,
        		$task->TaskPriority->name,
        		showDate($task->start_date),
        		showDate($task->due_date),
        		showDateTime($task->complete_date),
        		$progress,
        		getRatingStar($rating)
        		);
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function topChart(){

		$chart = array();
		foreach(\App\User::all() as $user){
			$rating = 0;
			$total_task = $user->Task->count();
			foreach($user->Task as $task){
				if($task->sub_task_rating)
					$rating += getSubTaskRating($task->id,$user->id,1);
				else
					$rating += $task->pivot->rating;
			}

			$average_rating = ($total_task) ? $rating/$total_task : 0;

			$chart[] = array('rating' => $average_rating,
								'id' => $user->id,
								'name' => $user->full_name_with_designation,
								'task' => $user->Task->count()
								);
		}

		usort($chart, function($a, $b) {
		    if($a['rating']==$b['rating']) return 0;
		    return $a['rating'] < $b['rating']?1:-1;
		});

		$i = 0;
		$j = 1;
		$top_chart = array();
		foreach($chart as $key => $value){
			$i++;
			if($i <= 5 && $value['rating']){
				$value['rank'] = $j;
				$top_chart[] = $value;
				$j++;
			}

		}

		return view('task.top_chart',compact('top_chart'));
	}
}