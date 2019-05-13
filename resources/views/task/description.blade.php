
       	<h2><strong>{!!trans('messages.assigned').'</strong> '.trans('messages.user')!!}

        </h2>
        <div style="padding:5px 10px;">
        	@if($task->User->count())
	        	@foreach($task->user as $user)
	        		<div class="row" style="margin-bottom: 5px;">
	        			<div style="width:60px;float:left;">
	        				{!! getAvatar($user->id,45) !!}
	        			</div>
	        			<p>{{$user->full_name_with_designation}}</p>

                        @if($task->user_id == Auth::user()->id)
                            <a href="#" data-href="/task-user-email/{{$task->id}}/{{$user->id}}" data-toggle="modal" data-target="#myModal" style="color:inherit;"><i class="fa fa-envelope fa-lg" data-toggle="tooltip" title="{{trans('messages.email').' '.trans('messages.user')}}"></i> </a>
                        @endif
	        		</div>
	        	@endforeach
        	@else
        		@if($task->user_id == Auth::user()->id)
        			<div class="alert alert-danger"><i class="fa fa-times icon"></i> <a href="#" data-href="/task/{{$task->id}}/edit" data-toggle="modal" data-target="#myModal" style="color: inherit;">{{trans('messages.assign_user_info')}}</a></div>
        		@endif
        	@endif
        </div>
       	<h2><strong>{!!trans('messages.task').'</strong> '.trans('messages.description')!!}</h2>
		<div class="custom-scrollbar" style="margin-top: 15px;">
        	<div class="the-notes info">
				{!! $task->description !!}
			</div>
        </div>