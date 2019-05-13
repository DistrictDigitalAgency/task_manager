@if(!getMode())
	<div class="row">
		<div class="col-md-12">
			<div class="box-info">
				@include('global.notification',['message' => 'You are free to perform all actions. The demo gets reset in every 30 minutes.' ,'type' => 'info'])
			</div>
		</div>
	</div>
@endif

@if(config('config.setup_guide') && defaultRole())
	<div class="row" id="setup_panel">
		<div class="col-md-12">
    		<div class="box-info">
    			<h2>
					<strong>{!! trans('messages.setup_guide') !!}</strong>
					<div class="additional-btn">
					{!! Form::open(['route' => 'setup-guide','role' => 'form', 'class'=>'form-inline','id' => 'setup-guide-form','data-setup-guide-complete' => 1]) !!}
					<button type="submit" class="btn btn-danger btn-sm">{{ trans('messages.hide') }}</button>
					{!! Form::close() !!}
					</div>
    			</h2>
    			<div id="setup_guide">
					{!! $setup_guide !!}
				</div>
			</div>
		</div>
	</div>
@endif