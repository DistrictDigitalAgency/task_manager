<!DOCTYPE html>
<html>
    <head>
    <title>{!! config('config.application_name') ? : config('constants.default_title') !!}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {!! Html::style('assets/css/bootstrap.min.css') !!}
    {!! HTML::style('assets/vendor/jquery-ui/jquery-ui.min.css') !!}
    {!! Html::style('assets/css/style.css') !!}
    {!! Html::style('assets/css/style-responsive.css') !!}
    {!! Html::style('assets/css/animate.css') !!}
    {!! HTML::style('assets/vendor/toastr/toastr.min.css') !!}

    @if(isset($direction) && $direction == 'rtl')
    {!! HTML::style('assets/css/bootstrap-rtl.css') !!}
    {!! HTML::style('assets/css/bootstrap-flipped.css') !!}
    {!! HTML::style('assets/css/style-right.css') !!}
    @endif

    {!! Html::style('assets/vendor/font-awesome/css/font-awesome.min.css') !!}
    {!! Html::style('assets/vendor/sortable/sortable-theme-bootstrap.css') !!}
    {!! Html::style('assets/vendor/icheck/skins/flat/blue.css') !!}
    {!! Html::style('assets/vendor/select/css/bootstrap-select.min.css') !!}
    {!! Html::style('assets/vendor/switch/bootstrap-switch.min.css') !!}
    {!! Html::style('assets/vendor/datepicker/css/datepicker.css') !!}
    @if(isset($assets) && in_array('datatable',$assets))
        {!! Html::style('assets/vendor/datatables/datatables.min.css') !!}
    @endif
    @if(isset($assets) && in_array('calendar',$assets))
        {!! Html::style('assets/vendor/calendar/fullcalendar.min.css') !!}
    @endif
    @if(isset($assets) && in_array('tags',$assets))
        {!! Html::style('assets/vendor/tags/tags.css') !!}
    @endif
    @if(isset($assets) && in_array('slider',$assets))
        {!! Html::style('assets/vendor/slider/bootstrap-slider.min.css') !!}
    @endif
    {!! Html::style('assets/vendor/page/page.css') !!}
    @if(isset($assets) && in_array('summernote',$assets))
        {!! Html::style('assets/vendor/summernote/summernote.css') !!}
    @endif
    {!! Html::style('assets/css/custom.css') !!}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    </head>