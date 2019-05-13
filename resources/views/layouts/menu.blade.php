
	<li {!! (in_array('home',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'home') !!}><a href="/home"><i class="fa fa-home icon"></i> {!! trans('messages.home') !!}</a></li>
	<li {!! (in_array('user',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'user') !!}><a href="/user"><i class="fa fa-user icon"></i> {!! trans('messages.user') !!}</a></li>
    @if(config('config.enable_message'))
    	<li {!! (in_array('message',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'message') !!}><a href="/message"><i class="fa fa-envelope icon"></i> {!! trans('messages.message') !!}</a></li>
    @endif
    @if(Entrust::can('list-department'))
		<li {!! (in_array('department',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'department') !!}><a href="/department"><i class="fa fa-bank icon"></i> {!! trans('messages.department') !!}</a></li>
	@endif
    @if(Entrust::can('list-designation'))
		<li {!! (in_array('designation',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'designation') !!}><a href="/designation"><i class="fa fa-sitemap icon"></i> {!! trans('messages.designation') !!}</a></li>
	@endif
    @if(Entrust::can('list-location'))
		<li {!! (in_array('location',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'location') !!}><a href="/location"><i class="fa fa-code-fork icon"></i> {!! trans('messages.location') !!}</a></li>
	@endif
    @if(Entrust::can('list-announcement'))
		<li {!! (in_array('announcement',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'announcement') !!}><a href="/announcement"><i class="fa fa-bullhorn icon"></i> {!! trans('messages.announcement') !!}</a></li>
	@endif
    @if(Entrust::can('list-task'))
		<li {!! (in_array('task',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'task') !!}><a href="/task"><i class="fa fa-tasks icon"></i> {!! trans('messages.task') !!}</a></li>
	@endif
	@if(Entrust::can('access-report'))
		<li {!! (in_array('report',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'report') !!}><a href=""><i class="fa fa-bars icon"></i><i class="fa fa-angle-double-down i-right"></i> {!! trans('messages.report') !!}</a>
			<ul {!! (
						in_array('user_task_rating',$menu) ||
						in_array('user_task',$menu)
			) ? 'class="visible"' : '' !!}>
				<li class="no-sort {!! (in_array('user_task_rating',$menu)) ? 'active' : '' !!}"><a href="/user-task-rating"><i class="fa fa-angle-right"></i> {!! trans('messages.user').' '.trans('messages.task').' '.trans('messages.rating') !!} </a></li>
				<li class="no-sort {!! (in_array('user_task_summary',$menu)) ? 'active' : '' !!}"><a href="/user-task-summary"><i class="fa fa-angle-right"></i> {!! trans('messages.user').' '.trans('messages.task').' '.trans('messages.summary') !!} </a></li>
			</ul>
		</li>
	@endif
    @if(Entrust::can('manage-configuration'))
		<li {!! (in_array('configuration',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'configuration') !!}><a href="/configuration"><i class="fa fa-cogs icon"></i> {!! trans('messages.configuration') !!}</a></li>
	@endif