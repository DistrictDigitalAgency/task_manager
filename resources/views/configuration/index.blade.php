@extends('layouts.app')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/home">{!! trans('messages.home') !!}</a></li>
		    <li class="active">{!! trans('messages.configuration') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<div class="tabs-left">	
						<ul class="nav nav-tabs col-md-2" style="padding-right:0;">
		                    <li class="active"><a href="#general-tab" data-toggle="tab">{{trans('messages.general')}}</a>
		                    </li>
		                    <li><a href="#logo-tab" data-toggle="tab">{{trans('messages.logo')}}</a>
		                    </li>
		                    <li><a href="#system-tab" data-toggle="tab">{{trans('messages.system')}}</a>
		                    </li>
		                    <li><a href="#mail-tab" data-toggle="tab">{{trans('messages.mail')}}</a>
		                    </li>
		                    <li><a href="#sms-tab" data-toggle="tab">SMS</a>
		                    </li>
		                    <li><a href="#auth-tab" data-toggle="tab">{{trans('messages.authentication')}}</a>
		                    </li>
		                    <li><a href="#menu-tab" data-toggle="tab">{{trans('messages.menu')}}</a>
		                    </li>
		                    <li><a href="#task-tab" data-toggle="tab">{{trans('messages.task')}}</a>
		                    </li>
		                </ul>

				        <div id="myTabContent" class="tab-content col-md-10 col-xs-10" style="padding:0px 25px 10px 25px;">
						  <div class="tab-pane active animated fadeInRight" id="general-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.general') }}</strong> {{ trans('messages.configuration') }}</h2>
						    	{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-general-form','id' => 'config-general-form','data-no-form-clear' => 1]) !!}
                                    @include('configuration._general_form')
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="logo-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.logo') }}</strong></h2>
						    	{!! Form::open(['files' => true, 'route' => 'configuration.logo','role' => 'form', 'class'=>'config-logo-form','id' => 'config-logo-form','data-submit' => 'noAjax']) !!}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="file" class="btn btn-default" name="logo" id="logo" data-buttonText="{!! trans('messages.select').' '.trans('messages.logo') !!}">
                                            </div>
                                        </div>
                                    </div>
                                    @if(config('config.logo') && File::exists(config('constant.upload_path.logo').config('config.logo')))
                                    <div class="form-group">
                                        <img src="{{ URL::to(config('constant.upload_path.logo').config('config.logo')) }}" width="150px" style="margin-left:20px;">
                                        <div class="checkbox">
                                            <label>
                                              <input name="remove_logo" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1" data-off-value="0"> {!! trans('messages.remove').' '.trans('messages.logo') !!}
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="system-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.system') }}</strong> {{ trans('messages.configuration') }}</h2>
						    	{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-system-form','id' => 'config-system-form','data-disable-enter-submission' => '1','data-no-form-clear' => 1]) !!}
                                    @include('configuration._system_form')
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="mail-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{trans('messages.mail')}}</strong> {{ trans('messages.mail') }}</h2>
						    	{!! Form::open(['route' => 'configuration.mail','role' => 'form', 'class'=>'config-mail-form','id' => 'config-mail-form','data-no-form-clear' => 1]) !!}
                                    @include('configuration._mail_form')
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="sms-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>SMS</strong> {{ trans('messages.configuration') }}</h2>
						    	{!! Form::open(['route' => 'configuration.sms','role' => 'form', 'class'=>'config-sms-form','id' => 'config-sms-form','data-no-form-clear' => 1]) !!}
                                    @include('configuration._sms_form')
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="auth-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.authentication') }}</strong></h2>
						    	{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-auth-form','id' => 'config-auth-form','data-no-form-clear' => 1]) !!}
                                    @include('configuration._auth_form')
                                {!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="menu-tab">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.menu') }}</strong> {{ trans('messages.configuration') }}</h2>
						    	{!! Form::open(['route' => 'configuration.menu','role' => 'form', 'class'=>'config-menu-form','id' => 'config-menu-form','data-draggable' => 1,'data-no-form-clear' => 1,'data-sidebar' => 1]) !!}
								<div class="draggable-container">
									<?php $i = 0; ?>
									@foreach(\App\Menu::orderBy('order')->orderBy('id')->get() as $menu_item)
										<?php $i++; ?>
									  <div class="draggable" data-name="{{$menu_item->name}}" data-index="{{$i}}">
									    <p><input type="checkbox" class="icheck" name="{{$menu_item->name}}-visible" value="1" {{($menu_item->visible) ? 'checked' : ''}}> <span style="margin-left:50px;">{{toWord($menu_item->name)}}</span></p>
									  </div>
									@endforeach
								</div>
								{!! Form::hidden('config_type','menu')!!}
			  					{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="task-tab">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.task').' '.trans('messages.configuration') !!}</strong></h2>
						    	<div class="row">
									<div class="col-sm-4">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.task').' '.trans('messages.category') !!} </h2>
											{!! Form::open(['route' => 'task-category.store','class'=>'task-category-form','id' => 'task-category-form','data-table-refresh' => 'task-category-table']) !!}
												@include('task_category._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-8">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all').'</strong> '.trans('messages.task').' '.trans('messages.category') !!} </h2>
											<div class="table-responsive">
												<table data-sortable class="table table-hover table-striped ajax-table show-table" id="task-category-table" data-source="/task-category/lists" data-extra="&type=starred">
													<thead>
														<tr>
															<th>{!! trans('messages.category') !!}</th>
															<th>{!! trans('messages.description') !!}</th>
															<th data-sortable="false">{!! trans('messages.option') !!}</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.task').' '.trans('messages.priority') !!} </h2>
											{!! Form::open(['route' => 'task-priority.store','class'=>'task-priority-form','id' => 'task-priority-form','data-table-refresh' => 'task-priority-table']) !!}
												@include('task_priority._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-8">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all').'</strong> '.trans('messages.task').' '.trans('messages.priority') !!} </h2>
											<div class="table-responsive">
												<table data-sortable class="table table-hover table-striped ajax-table" id="task-priority-table" data-source="/task-priority/lists">
													<thead>
														<tr>
															<th>{!! trans('messages.priority') !!}</th>
															<th>{!! trans('messages.description') !!}</th>
															<th data-sortable="false">{!! trans('messages.option') !!}</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						</div>
					</div>
				</div>
			</div>
		</div>

	@stop