
			<div class="row">
				<div class="col-md-6">
				  <div class="form-group">
				    {!! Form::label('name',trans('messages.location'),[])!!}
					{!! Form::input('text','name',isset($location->name) ? $location->name : '',['class'=>'form-control','placeholder'=>trans('messages.location')])!!}
				  </div>
				</div>
				<div class="col-md-6">
				  <div class="form-group">
				    {!! Form::label('top_location_id',trans('messages.top').' '.trans('messages.location'),[])!!}
					{!! Form::select('top_location_id', $top_locations,(isset($location->top_location_id)) ? $location->top_location_id : '',['class'=>'form-control input-xlarge show-tick','title'=>trans('messages.select_one')])!!}
				  </div>
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('address',trans('messages.address'),[])!!}
				<div class="row">
					<div class="col-md-6">
						{!! Form::input('text','address_line_1',(isset($location->address_line_1)) ? $location->address_line_1 : '',['class'=>'form-control','placeholder'=>trans('messages.address_line_1')])!!}
					</div>
					<div class="col-md-6">
						{!! Form::input('text','address_line_2',(isset($location->address_line_2)) ? $location->address_line_2 : '',['class'=>'form-control','placeholder'=>trans('messages.address_line_2')])!!}
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-xs-3">
					{!! Form::input('text','city',(isset($location->city)) ? $location->city : '',['class'=>'form-control','placeholder'=>trans('messages.city')])!!}
					</div>
					<div class="col-xs-3">
					{!! Form::input('text','state',(isset($location->state)) ? $location->state : '',['class'=>'form-control','placeholder'=>trans('messages.state')])!!}
					</div>
					<div class="col-xs-3">
					{!! Form::input('text','zipcode',(isset($location->zipcode)) ? $location->zipcode : '',['class'=>'form-control','placeholder'=>trans('messages.zipcode')])!!}
					</div>
					<div class="col-xs-3">
					{!! Form::select('country_id', config('country'),(isset($location->country_id)) ? $location->country_id : '',['class'=>'form-control show-tick','title'=>trans('messages.country')])!!}
					</div>
				</div>
			</div>
		  	{{ getCustomFields('location-form',$custom_field_values) }}
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
