
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.task').' '.trans('messages.priority'),[])!!}
				{!! Form::input('text','name',isset($task_priority->name) ? $task_priority->name : '',['class'=>'form-control','placeholder'=>trans('messages.task').' '.trans('messages.priority')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('description',trans('messages.description'),[])!!}
			    {!! Form::textarea('description',isset($task_priority->description) ? $task_priority->description : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
			    <span class="countdown"></span>
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
			  	
