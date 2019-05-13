@extends('layouts.app')

@section('content')
<div class="container">

        
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    @if(defaultRole())
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x info"></i>
                                  <i class="fa fa-tasks fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::count()}}</h3>
                                <p>{{trans('messages.total').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x warning"></i>
                                  <i class="fa fa-battery-half fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::where('progress','<',100)->count()}}</h3>
                                <p>{{trans('messages.pending').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x danger"></i>
                                  <i class="fa fa-fire fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::where('progress','<',100)->where('due_date','<',date('Y-m-d'))->count()}}</h3>
                                <p>{{trans('messages.overdue').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x success"></i>
                                  <i class="fa fa-battery-full fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::where('progress','=',100)->count()}}</h3>
                                <p>{{trans('messages.complete').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x warning"></i>
                                  <i class="fa fa-user-plus fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::doesntHave('user')->count()}}</h3>
                                <p>{{trans('messages.unassigned').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x info"></i>
                                  <i class="fa fa-user fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{\App\Task::whereUserId(Auth::user()->id)->count()}}</h3>
                                <p>{{trans('messages.owned').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    @else
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x info"></i>
                                  <i class="fa fa-tasks fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{$tasks->count()}}</h3>
                                <p>{{trans('messages.total').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x warning"></i>
                                  <i class="fa fa-battery-half fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{ $tasks->filter(function($item){
                                            return (data_get($item, 'progress') < '100');
                                        })->count() }}</h3>
                                <p>{{trans('messages.pending').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x danger"></i>
                                  <i class="fa fa-fire fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{ $tasks->filter(function($item){
                                            return (data_get($item, 'progress') < '100');
                                        })->filter(function($item) {
                                            return (data_get($item, 'due_date') < date('Y-m-d'));
                                        })->count()
                                 }}</h3>
                                <p>{{trans('messages.overdue').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="box-info">
                            <div class="icon-box">
                                <span class="fa-stack">
                                  <i class="fa fa-circle fa-stack-2x success"></i>
                                  <i class="fa fa-battery-full fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div class="text-box">
                                <h3>{{ $tasks->filter(function($item){
                                            return (data_get($item, 'progress') > '99');
                                        })->count() }}</h3>
                                <p>{{trans('messages.complete').' '.trans('messages.task')}}</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="box-info">
                    <h2><strong>{{trans('messages.top')}}</strong> {{trans('messages.rating')}}</h2>
                    <div id="load-task-top-chart" data-source="/task/top-chart" class="custom-scrollbar" style="max-height: 235px;height: 235px;"></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box-info full">
                    <ul class="nav nav-tabs nav-justified">
                      <li class="active"><a href="#starred-task-tab" data-toggle="tab"><i class="fa fa-star"></i> {!! trans('messages.starred').' '.trans('messages.task') !!}</a></li>
                      <li><a href="#pending-task-tab" data-toggle="tab"><i class="fa fa-battery-half"></i> {!! trans('messages.pending').' '.trans('messages.task') !!}</a></li>
                      <li><a href="#overdue-task-tab" data-toggle="tab"><i class="fa fa-fire"></i> {!! trans('messages.overdue').' '.trans('messages.task') !!}</a></li>
                      <li><a href="#owned-task-tab" data-toggle="tab"><i class="fa fa-user"></i> {!! trans('messages.owned').' '.trans('messages.task') !!}</a></li>
                      <li><a href="#unassigned-task-tab" data-toggle="tab"><i class="fa fa-user-plus"></i> {!! trans('messages.unassigned').' '.trans('messages.task') !!}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane animated active fadeInRight" id="starred-task-tab">
                            <div class="user-profile-content custom-scrollbar">
                                <div class="table-responsive">
                                    <table data-sortable class="table table-bordered table-hover table-striped ajax-table show-table" id="task-starred-table" data-source="/task/fetch" data-extra="&type=starred">
                                        <thead>
                                            <tr>
                                                <th>{!! trans('messages.title') !!}</th>
                                                <th>{!! trans('messages.status') !!}</th>
                                                <th>{!! trans('messages.category') !!}</th>
                                                <th>{!! trans('messages.priority') !!}</th>
                                                <th>{!! trans('messages.progress') !!}</th>
                                                <th>{!! trans('messages.start').' '.trans('messages.date') !!}</th>
                                                <th>{!! trans('messages.due').' '.trans('messages.date') !!}</th>
                                                <th data-sortable="false">{!! trans('messages.option') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane animated fadeInRight" id="pending-task-tab">
                            <div class="user-profile-content custom-scrollbar">
                                <div class="table-responsive">
                                    <table data-sortable class="table table-bordered table-hover table-striped ajax-table show-table" id="task-pending-table" data-source="/task/fetch" data-extra="&type=pending">
                                        <thead>
                                            <tr>
                                                <th>{!! trans('messages.title') !!}</th>
                                                <th>{!! trans('messages.status') !!}</th>
                                                <th>{!! trans('messages.category') !!}</th>
                                                <th>{!! trans('messages.priority') !!}</th>
                                                <th>{!! trans('messages.progress') !!}</th>
                                                <th>{!! trans('messages.start').' '.trans('messages.date') !!}</th>
                                                <th>{!! trans('messages.due').' '.trans('messages.date') !!}</th>
                                                <th data-sortable="false">{!! trans('messages.option') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane animated fadeInRight" id="overdue-task-tab">
                            <div class="user-profile-content custom-scrollbar">
                                <div class="table-responsive">
                                    <table data-sortable class="table table-bordered table-hover table-striped ajax-table show-table" id="task-overdue-table" data-source="/task/fetch" data-extra="&type=overdue">
                                        <thead>
                                            <tr>
                                                <th>{!! trans('messages.title') !!}</th>
                                                <th>{!! trans('messages.status') !!}</th>
                                                <th>{!! trans('messages.category') !!}</th>
                                                <th>{!! trans('messages.priority') !!}</th>
                                                <th>{!! trans('messages.progress') !!}</th>
                                                <th>{!! trans('messages.start').' '.trans('messages.date') !!}</th>
                                                <th>{!! trans('messages.due').' '.trans('messages.date') !!}</th>
                                                <th data-sortable="false">{!! trans('messages.option') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane animated fadeInRight" id="owned-task-tab">
                            <div class="user-profile-content custom-scrollbar">
                                <div class="table-responsive">
                                    <table data-sortable class="table table-bordered table-hover table-striped ajax-table show-table" id="task-owned-table" data-source="/task/fetch" data-extra="&type=owned">
                                        <thead>
                                            <tr>
                                                <th>{!! trans('messages.title') !!}</th>
                                                <th>{!! trans('messages.status') !!}</th>
                                                <th>{!! trans('messages.category') !!}</th>
                                                <th>{!! trans('messages.priority') !!}</th>
                                                <th>{!! trans('messages.progress') !!}</th>
                                                <th>{!! trans('messages.start').' '.trans('messages.date') !!}</th>
                                                <th>{!! trans('messages.due').' '.trans('messages.date') !!}</th>
                                                <th data-sortable="false">{!! trans('messages.option') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane animated fadeInRight" id="unassigned-task-tab">
                            <div class="user-profile-content custom-scrollbar">
                                <div class="table-responsive">
                                    <table data-sortable class="table table-bordered table-hover table-striped ajax-table show-table" id="task-unassigned-table" data-source="/task/fetch" data-extra="&type=unassigned">
                                        <thead>
                                            <tr>
                                                <th>{!! trans('messages.title') !!}</th>
                                                <th>{!! trans('messages.status') !!}</th>
                                                <th>{!! trans('messages.category') !!}</th>
                                                <th>{!! trans('messages.priority') !!}</th>
                                                <th>{!! trans('messages.progress') !!}</th>
                                                <th>{!! trans('messages.start').' '.trans('messages.date') !!}</th>
                                                <th>{!! trans('messages.due').' '.trans('messages.date') !!}</th>
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

        <div class="row">
            <div class="col-sm-6">
                <div class="box-info">
                    <h2><strong>{!! trans('messages.announcement') !!}</strong> </h2>
                    <div class="custom-scrollbar">
                    @if(count($announcements))
                        @foreach($announcements as $announcement)
                            <div class="the-notes info">
                                <h4><a href="#" data-href="/announcement/{{$announcement->id}}" data-toggle="modal" data-target="#myModal">{!! $announcement->title !!}</a></h4>
                                <span style="color:green;"><i class="fa fa-clock-o"></i> {!! showDateTime($announcement->created_at) !!}</span>
                                <p class="time pull-right" style="text-align:right;">{!! trans('messages.by').' '.$announcement->User->full_name.'<br />'.$announcement->User->full_designation !!}</p>
                            </div>
                        @endforeach
                    @else
                        @include('global.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
                    @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box-info">
                    <h2><strong>{!! trans('messages.company').' '.trans('messages.hierarchy') !!}</strong></h2>
                    <div class="custom-scrollbar" >
                        <p class="alert alert-info"><strong>{!! Auth::user()->full_name.', '.trans('messages.no_of_user_under_you').' : '.$child_staff_count !!}
                        </strong></p>
                        <h4><strong>{!! trans('messages.you').' : '.Auth::user()->Profile->Designation->full_designation !!}
                        </strong></h4>
                        {!! createLineTreeView($tree,Auth::user()->Profile->designation_id) !!}
                    </div>
                </div>
            </div>
        </div>
    <div class="row">
        <div class="col-md-8">
            <div class="box-info">
                <h2>
                    {{trans('messages.calendar')}}
                </h2>
                <div id="render_calendar">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if(config('config.enable_group_chat'))
            <div class="box-info">
                <h2>
                    <strong>{{trans('messages.group')}}</strong> {{trans('messages.chat')}}
                </h2>
                <div id="chat-box" class="chat-widget custom-scrollbar">
                    <div id="chat-messages" data-chat-refresh="{{config('config.enable_chat_refresh')}}" data-chat-refresh-duration="{{ config('config.chat_refresh_duration') }}"></div>
                </div>
                {!! Form::open(['route' => 'chat.store','role' => 'form', 'class'=>'chat-form input-chat','id' => 'chat-form','data-refresh' => 'chat-messages']) !!}
                {!! Form::input('text','message','',['class'=>'form-control','data-autoresize' => 1,'placeholder' => 'Type your message here..'])!!}
                {!! Form::close() !!}
            </div>
            @endif

            @if(count($celebrations))
            <div class="box-info">
                <h2>
                    <strong>{{trans('messages.celebration')}}</strong>
                </h2>
                <div id="chat-box" class="chat-widget custom-scrollbar">
                    <ul class="media-list">
                    @foreach($celebrations as $celebration)
                      <li class="media">
                        <a class="pull-left" href="#">
                          {!! getAvatar($celebration['id'],55) !!}
                        </a>
                        <div class="media-body success">
                          <p class="media-heading"><i class="fa fa-{{ $celebration['icon'] }} icon" style="margin-right:10px;"></i> {{ $celebration['title'] }} ({!! $celebration['number'] !!})</p>
                          <p style="margin-bottom:5px;"><strong>{!! $celebration['name'] !!}</strong></p>
                        </div>
                      </li>
                    @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
