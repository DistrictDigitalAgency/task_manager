@extends('layouts.guest')
    @section('content')
        <div class="full-content-center animated bounceIn">
            @if(logoExists())
            <a href="/"><img src="/{!! config('constant.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <h2>{{trans('messages.error')}}</h2>
            <p>{{trans('messages.error_message')}}</p>
            <p>{{trans('messages.back_to')}} <a href="/home">{{trans('messages.home')}}</a></p>
        </div>
        @include('layouts.footer')
    @stop
