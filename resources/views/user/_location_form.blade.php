				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label {!! !isset($user_location) ? 'class="sr-only"' : '' !!} for="from_date">{!! trans('messages.from').' '.trans('messages.date') !!}</label>
							<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from').' '.trans('messages.date') !!}" readonly="true" value="{!! isset($user_location->from_date) ? $user_location->from_date : '' !!}">
					  	</div>
					</div>
					<div class="col-sm-4">
					  	<div class="form-group">
							<label {!! !isset($user_location) ? 'class="sr-only"' : '' !!} for="to_date">{!! trans('messages.to').' '.trans('messages.date') !!}</label>
							<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to').' '.trans('messages.date') !!}" readonly="true" value="{!! isset($user_location->to_date) ? $user_location->to_date : '' !!}">
					  	</div>
				  	</div>
					<div class="col-sm-4">
					  	<div class="form-group">
						    {!! Form::label('location_id',trans('messages.location'),['class' => !isset($user_location) ? 'sr-only' : ''])!!}
							{!! Form::select('location_id', $locations,isset($user_location->location_id) ? $user_location->location_id : '',['class'=>'form-control show-tick','title'=>trans('messages.select_one')])!!}
						</div>
					</div>
				</div>
				<div class="form-group">
				    {!! Form::label('description',trans('messages.description'),[])!!}
				    {!! Form::textarea('description',isset($user_location->description) ? $user_location->description : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
				    <span class="countdown"></span>
				</div>
				{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
				<div class="clear"></div>