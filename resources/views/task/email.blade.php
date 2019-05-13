
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.send').' '.trans('messages.email') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($task,['method' => 'POST','route' => ['task.user-email',$task->id,$user_id] ,'class' => 'task-user-email-form','id' => 'task-user-email-form','data-refresh' => 'load-task-activity','data-user-id' => $user_id,'data-url' => '/task-email-content','data-task-id' => $task->id]) !!}
			<div class="form-group">
                {!! Form::select('template_id', $templates,'',['class'=>'form-control show-tick','id'=>'template_id','title' => trans('messages.select_one')])!!}
            </div>
            <div class="form-group">
                {!! Form::input('text','subject','',['class'=>'form-control','placeholder'=>trans('messages.subject'),'id' => 'mail_subject']) !!}
            </div>
            <div class="form-group">
                {!! Form::textarea('body','',['size' => '30x3', 'class' => 'form-control summernote', 'id' => 'mail_body', 'placeholder' => trans('messages.body')])!!}
            </div>

            {!! Form::submit(trans('messages.send'),['class' => 'btn btn-primary pull-right']) !!}
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>