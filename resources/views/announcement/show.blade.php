
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! $announcement->title !!}</h4>
	</div>
	<div class="modal-body">
		{!! $announcement->description !!}
		<span class="timeinfo"><i class="fa fa-clock-o"></i> {!! showDateTime($announcement->created_at) !!}</span>
		<span class="pull-right">
		{!! $announcement->User->full_name_with_designation !!}
		</span>
		<div class="clear"></div>
	</div>