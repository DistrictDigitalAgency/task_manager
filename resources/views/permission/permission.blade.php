@extends('layouts.app')

    @section('breadcrumb')
        <ul class="breadcrumb">
		    <li><a href="/home">{!! trans('messages.home') !!}</a></li>
		    <li class="active">{!! trans('messages.permission') !!}</li>
		</ul>
    @stop

    @section('content')
        <div class="row">
        	<div class="col-sm-12">
				<div class="box-info full">
                    <h2>
                        <strong>{!!trans('messages.save').'</strong> '.trans('messages.permission')!!}
                        <div class="additional-btn">
                        </div>
                    </h2>
                    {!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-general-form','id' => 'config-general-form','data-no-form-clear' => 1]) !!}
						  <div class="row" style="padding:10px;">
						  	<div class="col-md-6">
							  <div class="form-group">
							    {!! Form::label('subordinate_level',trans('messages.subordinate').' '.trans('messages.level'),['class' => 'control-label '])!!}
				                <div class="checkbox">
				                <input name="subordinate_level" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1" {{ (config('config.subordinate_level') == 1) ? 'checked' : '' }} data-off-value="0">
				                </div>
				              </div>
							  <div class="form-group">
							    {!! Form::label('location_level',trans('messages.location').' '.trans('messages.level'),['class' => 'control-label '])!!}
				                <div class="checkbox">
				                <input name="location_level" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1" {{ (config('config.location_level') == 1) ? 'checked' : '' }} data-off-value="0">
				                </div>
				              </div>
						  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
						  	</div>
						  </div>
                    {!! Form::close() !!}

                    {!! Form::open(['route' => 'permission.save-permission','role' => 'form', 'class'=>'permission-form','id' => 'permission-form','data-no-form-clear' => 1]) !!}
                    	<table class="table table-hover table-striped">
					  	<thead>
					  		<tr>
					  			<th>{!! trans('messages.permission') !!}</th>
					  			@foreach(\App\Role::all() as $role)
					  			<th>{!! toWord($role->name) !!}</th>
					  			@endforeach
					  		</tr>
					  	</thead>
					  	<tbody>
					  		@foreach($permissions as $permission)
					  			@if($permission->category != $category)
					  			<tr style="background-color:#3498DB;color:#ffffff;"><td colspan="{!! count(\App\Role::all())+1 !!} "><strong>{!! toWord($permission->category).' '.trans('messages.module') !!}</strong></td></tr>
					  			<?php $category = $permission->category; ?>
					  			@endif
					  			<tr>
					  				<td>{!! toWord($permission->name) !!}</td>
						  			@foreach(\App\Role::all() as $role)
						  			<th><input class="icheck" type="checkbox" name="permission[{!!$role->id!!}][{!!$permission->id!!}]" value = '1' {!! (in_array($role->id.'-'.$permission->id,$permission_role)) ? 'checked' : '' !!} @if($role->is_hidden) disabled @endif></th>
						  			@endforeach
					  			</tr>
					  		@endforeach
					  	</tbody>
					  </table>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right','style' => 'margin:10px;']) !!}
                    {!! Form::close() !!}
                </div>
			</div>
        </div>
    @stop