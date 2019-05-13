	<div class="row">	
		<div class="col-md-6">
			<div class="form-group">
			{!! Form::label('title',trans('messages.title'),[])!!}
			{!! Form::input('text','title',isset($announcement->title) ? $announcement->title : '',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
					{!! Form::label('from_date',trans('messages.from').' '.trans('messages.date'),[])!!}
					{!! Form::input('text','from_date',isset($announcement->from_date) ? $announcement->from_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.from').' '.trans('messages.date'),'readonly' => 'true'])!!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('to_date',trans('messages.to').' '.trans('messages.date'),[])!!}
						{!! Form::input('text','to_date',isset($announcement->to_date) ? $announcement->to_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.to').' '.trans('messages.date'),'readonly' => 'true'])!!}
					</div>
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('designation_id',trans('messages.designation'),['class' => 'control-label'])!!}
				{!! Form::select('designation_id[]', $designations, isset($selected_designation) ? $selected_designation : '',['class'=>'form-control input-xlarge show-tick','title'=>trans('messages.select_one'),'multiple' => true,'data-actions-box' => "true"])!!}
				<div class="help-block">{!! trans('messages.leave_blank_for_all_designation') !!}</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('description',trans('messages.description'),[])!!}
				{!! Form::textarea('description',isset($announcement->description) ? $announcement->description : '',['size' => '30x10', 'class' => 'form-control summernote', 'placeholder' => trans('messages.description')])!!}
			</div>
		</div>
	</div>
	{{ getCustomFields('announcement-form',$custom_field_values) }}
	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
