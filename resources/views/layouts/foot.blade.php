    
    <div id="js-var" style="visibility:none;" 
        data-toastr-position="{{config('config.notification_position')}}"
        data-something-error-message="{{trans('messages.something_wrong')}}"
        data-character-remaining="{{trans('messages.character_remaining')}}"
        data-textarea-limit="{{config('config.textarea_limit')}}"
        data-processing-messsage="{{trans('messages.processing_message')}}"
        data-redirecting-messsage="{{trans('messages.redirecting_message')}}"
        data-show-error-message="{{config('config.show_error_messages')}}"
        data-menu="{{isset($menu) ? $menu : ''}}"
    ></div>

    {!! Html::script('assets/js/jquery.min.js') !!}
    {!! Html::script('assets/js/bootstrap.min.js') !!}
    {!! HTML::script('assets/vendor/jquery-ui/jquery-ui.min.js') !!}
    {!! Html::script('assets/vendor/slimscroll/jquery.slimscroll.min.js') !!}
    {!! Html::script('assets/vendor/sortable/sortable.min.js') !!}
    {!! Html::script('assets/vendor/select/js/bootstrap-select.min.js') !!}
    {!! HTML::script('assets/vendor/toastr/toastr.min.js') !!}
    @include('global.toastr_notification')
    {!! Html::script('assets/vendor/page/page.min.js') !!}
    @if(isset($assets) && in_array('summernote',$assets))
        {!! Html::script('assets/vendor/summernote/summernote.js') !!}
    @endif
    {!! Html::script('assets/vendor/password/password.js') !!}
    {!! Html::script('assets/vendor/input/bootstrap.file-input.js') !!}
    {!! Html::script('assets/vendor/switch/bootstrap-switch.min.js') !!}
    {!! Html::script('assets/vendor/datepicker/js/bootstrap-datepicker.js') !!}
    @if(isset($assets) && in_array('datatable',$assets))
        {!! Html::script('assets/vendor/datatables/datatables.min.js') !!}
    @endif
    @if(isset($assets) && in_array('calendar',$assets))
        {!! Html::script('assets/vendor/calendar/moment.min.js') !!}
        {!! Html::script('assets/vendor/calendar/fullcalendar.min.js') !!}
        {!! Html::script('assets/vendor/calendar/locale-all.js') !!}
    @endif
    @if(isset($assets) && in_array('recaptcha',$assets))
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
    @if(isset($assets) && in_array('tags',$assets))
        {!! Html::script('assets/vendor/tags/tags.min.js') !!}
    @endif
    @if(isset($assets) && in_array('slider',$assets))
        {!! Html::script('assets/vendor/slider/bootstrap-slider.min.js') !!}
    @endif
    @if(isset($assets) && in_array('form-wizard',$assets))
        {!! HTML::script('assets/vendor/wizard/jquery.snippet.js') !!}
        {!! HTML::script('assets/vendor/wizard/jquery.easyWizard.js') !!}
    @endif
    {!! Html::script('assets/js/bootbox.js') !!}
    {!! Html::script('assets/vendor/icheck/icheck.min.js') !!}
    <script>
    var calendar_events = {!! (isset($events)) ? json_encode($events) : '""' !!};
    </script>
    {!! Html::script('assets/js/wmlab.js') !!}
    @include('global.misc')
    </body>
</html>