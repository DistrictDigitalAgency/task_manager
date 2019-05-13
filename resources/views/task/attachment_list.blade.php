	@foreach($task->TaskAttachment as $task_attachment)
		<tr>
			<td>{{$task_attachment->title}}</td>
			<td>{{$task_attachment->description}}</td>
			<td>{{showDateTime($task_attachment->created_at)}}</td>
			<td>
				@if($task_attachment->user_id == Auth::user()->id)
					<div class="btn-group btn-group-xs">
						<a href="/task-attachment/download/{{$task_attachment->id}}" class="btn btn-xs btn-default" ><i class="fa fa-download"></i></a>
						{!!delete_form(['task-attachment.destroy',$task_attachment->id],['table-refresh' => 'task-attachment-table'])!!}
					</div>
				@endif
			</td>
		</tr>
	@endforeach