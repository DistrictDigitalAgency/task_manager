@extends('layouts.app')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/home">{!! trans('messages.home') !!}</a></li>
		    <li><a href="/user">{!! trans('messages.user') !!}</a></li>
		    <li class="active">{!! $user->full_name_with_designation !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info full">
					<div class="tabs-left">	
						<ul class="nav nav-tabs col-md-2" style="padding-right:0;">
						  <li class="active"><a href="#basic-tab" data-toggle="tab"> {{ trans('messages.basic') }} </a></li>
						  <li><a href="#avatar-tab" data-toggle="tab"> {{ trans('messages.avatar') }} </a></li>
						  <li><a href="#social-tab" data-toggle="tab"> {{ trans('messages.social') }} </a></li>
						  <li><a href="#custom-field-tab" data-toggle="tab"> {{ trans('messages.custom').' '.trans('messages.field') }} </a></li>
						  <li><a href="#location-tab" data-toggle="tab"> {{ trans('messages.location') }} </a></li>
                          @if($user->id != Auth::user()->id)
						  	<li><a href="#reset-password-tab" data-toggle="tab"> {{ trans('messages.reset').' '.trans('messages.password') }} </a></li>
						  @endif
						  @if(config('config.enable_email_template'))
						    <li><a href="#email-tab" data-toggle="tab">{{trans('messages.email')}}</a>
                            </li>
						  @endif
						</ul>
				        <div id="myTabContent" class="tab-content col-md-10 col-xs-10" style="padding:0px 25px 10px 25px;">
						  <div class="tab-pane active animated fadeInRight" id="basic-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.basic') }} </strong></h2>
						    	{!! Form::model($user,['method' => 'POST','route' => ['user.profile-update',$user->id] ,'class' => 'user-profile-form','id' => 'user-profile-form','data-no-form-clear' => 1]) !!}
                                    <div class="row">
                                        <div class="col-md-6">
                                        @if(!$user->hasRole(DEFAULT_ROLE))
                                            <div class="form-group">
                                                {!! Form::label('role',trans('messages.role'),[])!!}
                                                {!! Form::select('role_id[]',$roles,$user_roles,['class'=>'form-control show-tick','multiple' => 'multiple'])!!}
                                            </div>
                                        @endif
                                          <div class="form-group">
                                            {!! Form::label('name',trans('messages.name'),[])!!}
                                            <div class="row">
                                                <div class="col-md-6">
                                                {!! Form::input('text','first_name',$user->Profile->first_name,['class'=>'form-control','placeholder'=>trans('messages.first').' '.trans('messages.name')])!!}
                                                </div>
                                                <div class="col-md-6">
                                                {!! Form::input('text','last_name',$user->Profile->last_name,['class'=>'form-control','placeholder'=>trans('messages.last').' '.trans('messages.name')])!!}
                                                </div>
                                            </div>
                                          </div>
                                            <div class="form-group">
                                            {!! Form::label('work_phone',trans('messages.phone'))!!}
                                            <div class="row">
                                                <div class="col-xs-8">
                                                {!! Form::input('text','work_phone',$user->Profile->work_phone,['class'=>'form-control','placeholder'=>trans('messages.work')])!!}
                                                </div>
                                                <div class="col-xs-4">
                                                {!! Form::input('text','work_phone_extension',$user->Profile->work_phone_extension,['class'=>'form-control','placeholder'=>trans('messages.ext')])!!}
                                                </div>
                                            </div>
                                            <br />
                                            {!! Form::input('text','mobile',$user->Profile->mobile,['class'=>'form-control','placeholder'=>trans('messages.mobile')])!!}
                                            <div class="help-block">This will be used to send two factor auth code.</div>
                                            <br />
                                            {!! Form::input('text','home',$user->Profile->home,['class'=>'form-control','placeholder'=>trans('messages.home')])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        	<div class="form-group">
                                                {!! Form::label('designation_id',trans('messages.designation'),[])!!}
                                                {!! Form::select('designation_id',$designations,$user->Profile->designation_id,['class'=>'form-control show-tick'])!!}
                                            </div>
                                          <div class="form-group">
                                            {!! Form::label('date_of_birth',trans('messages.date_of_birth'),[])!!}
                                            {!! Form::input('text','date_of_birth',$user->Profile->date_of_birth,['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_birth')])!!}
                                          </div>
                                            <div class="form-group">
                                                {!! Form::label('address',trans('messages.address'),[])!!}
                                                {!! Form::input('text','address_1',$user->Profile->address_line_1,['class'=>'form-control','placeholder'=>trans('messages.address_line_1')])!!}
                                                <br />
                                                {!! Form::input('text','address_2',$user->Profile->address_line_2,['class'=>'form-control','placeholder'=>trans('messages.address_line_2')])!!}
                                                <br />
                                                <div class="row">
                                                    <div class="col-xs-5">
                                                    {!! Form::input('text','city',$user->Profile->city,['class'=>'form-control','placeholder'=>trans('messages.city')])!!}
                                                    </div>
                                                    <div class="col-xs-4">
                                                    {!! Form::input('text','state',$user->Profile->state,['class'=>'form-control','placeholder'=>trans('messages.state')])!!}
                                                    </div>
                                                    <div class="col-xs-3">
                                                    {!! Form::input('text','zipcode',$user->Profile->zipcode,['class'=>'form-control','placeholder'=>trans('messages.zipcode')])!!}
                                                    </div>
                                                </div>
                                                <br />
                                                {!! Form::select('country_id', [null => trans('messages.select_one')] + config('country'),$user->Profile->country_id,['class'=>'form-control show-tick','title'=>trans('messages.country')])!!}
                                            </div>
                                            {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
                                        </div>
                                    </div>                                        
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="avatar-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.avatar') }} </strong></h2>
						    	{!! Form::model($user,['files' => true, 'method' => 'POST','route' => ['user.avatar',$user->id] ,'class' => 'user-avatar-form','id' => 'user-avatar-form','data-submit' => 'noAjax']) !!}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="file" name="avatar" id="avatar" title="{!! trans('messages.select').' '.trans('messages.avatar') !!}" class="btn btn-default" data-buttonText="{!! trans('messages.select').' '.trans('messages.avatar') !!}">
                                            </div>
                                        </div>
                                    </div>
                                    @if($user->Profile->avatar && File::exists(config('constant.upload_path.avatar').$user->Profile->avatar))
                                    <div class="form-group">
                                        <img src="{!! URL::to(config('constant.upload_path.avatar').$user->Profile->avatar) !!}" width="150px" style="margin-left:20px;">
                                        <div class="checkbox">
                                            <label>
                                              <input name="remove_avatar" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1" data-off-value="0"> {!! trans('messages.remove').' '.trans('messages.avatar') !!}
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary']) !!}
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="social-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.social') }} </strong></h2>
						    	{!! Form::model($user,['method' => 'POST','route' => ['user.social-update',$user->id] ,'class' => 'user-social-form','id' => 'user-social-form','data-no-form-clear' => 1]) !!}
                                  <div class="form-group">
                                    {!! Form::label('facebook','Facebook',[])!!}
                                    {!! Form::input('text','facebook',$user->Profile->facebook,['class'=>'form-control','placeholder'=>'Facebook'])!!}
                                  </div>
                                  <div class="form-group">
                                    {!! Form::label('twitter','Twitter',[])!!}
                                    {!! Form::input('text','twitter',$user->Profile->twitter,['class'=>'form-control','placeholder'=>'Twitter'])!!}
                                  </div>
                                  <div class="form-group">
                                    {!! Form::label('google_plus','Google Plus',[])!!}
                                    {!! Form::input('text','google_plus',$user->Profile->google_plus,['class'=>'form-control','placeholder'=>'Google Plus'])!!}
                                  </div>
                                {{ getCustomFields('user-social-form',$custom_social_field_values) }}
                                {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="custom-field-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.custom').' '.trans('messages.field') }} </strong></h2>
						    	 {!! Form::model($user,['method' => 'POST','route' => ['user.custom-field-update',$user->id] ,'class' => 'user-custom-field-form','id' => 'user-custom-field-form','data-no-form-clear' => 1]) !!}
                                    {{ getCustomFields('user-registration-form',$custom_register_field_values) }}
                                    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="location-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.location') }} </strong></h2>
						    	{!! Form::model($user,['method' => 'POST','route' => ['user-location.store',$user->id] ,'class' => 'user-location-form','id' => 'user-location-form','data-table-refresh' => 'user-location-table']) !!}
									@include('user._location_form')
								{!! Form::close() !!}

								<br /><br />
								<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.location') !!}</h2>
								<div class="table-responsive">
									<table data-sortable class="table table-hover table-striped table-bordered ajax-table"  id="user-location-table" data-source="/user-location/lists" data-extra="&user_id={{$user->id}}">
										<thead>
											<tr>
												<th>{!! trans('messages.date') !!}</th>
												<th>{!! trans('messages.location') !!}</th>
												<th>{!! trans('messages.description') !!}</th>
												<th data-sortable="false" >{!! trans('messages.option') !!}</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
						    </div>
						  </div>
						  @if($user->id != Auth::user()->id)
						  <div class="tab-pane animated fadeInRight" id="reset-password-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.reset').' '.trans('messages.password') }} </strong></h2>
						    	{!! Form::model($user,['method' => 'POST','route' => ['user.force-change-password',$user->id] ,'class' => 'user-force-change-password-form','id' => 'user-force-change-password-form']) !!}
									<div class="form-group">
									    {!! Form::label('new_password',trans('messages.new').' '.trans('messages.password'),[])!!}
										{!! Form::input('password','new_password','',['class'=>'form-control '.(config('config.enable_password_strength_meter') ? 'password-strength' : ''),'placeholder'=>trans('messages.new').' '.trans('messages.password')])!!}
									</div>
									<div class="form-group">
									    {!! Form::label('new_password_confirmation',trans('messages.confirm').' '.trans('messages.password'),[])!!}
										{!! Form::input('password','new_password_confirmation','',['class'=>'form-control','placeholder'=>trans('messages.confirm').' '.trans('messages.password')])!!}
									</div>
									<div class="form-group">
										{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.update'),['class' => 'btn btn-primary pull-right']) !!}
									</div>
								{!! Form::close() !!}
						    </div>
						  </div>
						  @endif
						  @if(config('config.enable_email_template'))
						  	<div class="tab-pane animated fadeInRight" id="email-tab">
							    <div class="user-profile-content-wm">
							    	<h2><strong>{{ trans('messages.email').' '.trans('messages.user') }} </strong></h2>
							    	{!! Form::model($user,['method' => 'POST','route' => ['user.email',$user->id] ,'class' => 'user-email-form','id' => 'user-email-form','data-user-id' => $user->id,'data-url' => '/template/content']) !!}
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
							    </div>
						   	</div>
						  @endif
						</div>
					</div>
				</div>
			</div>
            <div class="col-sm-4">
                <div class="box-info full">
                    <h2>
                        <strong>{!!trans('messages.user').'</strong> '.trans('messages.profile')!!}
                    </h2>
                    <div class="row">
                        <div class="col-md-4  col-md-offset-3" style="margin-top:20px;margin-bottom:20px;">{!! getAvatar($user->id,150) !!}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-stripped table-hover show-table">
                            <tbody>
                                <tr>
                                    <th>{{trans('messages.name')}}</th>
                                    <td>{{$user->full_name}}</td>
                                </tr>
                                <tr>
                                    <th>{{trans('messages.status')}}</th>
                                    <td>{{toWord($user->status)}}</td>
                                </tr>
                                <tr>
                                    <th>{{trans('messages.designation')}}</th>
                                    <td>{{toWord($user->full_designation)}}</td>
                                </tr>
                                <tr>
                                    <th>{{trans('messages.role')}}</th>
                                    <td>
                                        @foreach($user->roles as $role)
                                            {{ucfirst($role->name)}}<br />
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{trans('messages.email')}}</th>
                                    <td>{{$user->email}}</td>
                                </tr>
                                @if(!config('config.login'))
                                <tr>
                                    <th>{{trans('messages.username')}}</th>
                                    <td>{{$user->username}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>{{trans('messages.signup').' '.trans('messages.date')}}</th>
                                    <td>{{showDate($user->created_at)}}</td>
                                </tr>
                                <tr>
                                    <th>{{trans('messages.last').' '.trans('messages.login')}}</th>
                                    <td>{{showDateTime($user->last_login)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(Auth::user()->id == $user->id)
                        <div class="row" style="padding:10px;">
                            <div class="col-md-6">
                                <a href="#" data-href="/change-password" class="btn btn-block btn-primary">{{trans('messages.change').' '.trans('messages.password')}}</a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();" class="btn btn-block btn-danger">{{trans('messages.logout')}}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
		</div>
	@stop