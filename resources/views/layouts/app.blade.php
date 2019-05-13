@include('layouts.head')
    <body class="tooltips">
        <div class="container">
            <div class="logo-brand header sidebar rows">
                <div class="logo">
                    <h1><a href="/">{{config('config.application_name')}}</a></h1>
                </div>
            </div>

            @include('layouts.sidebar')

            <div class="right content-page">

                @include('layouts.header')

                <div class="body content rows scroll-y">

                    @yield('breadcrumb')

                    @include('global.message')
                    
                    @yield('content')

                    @include('layouts.footer')

                </div>

            </div>
        <img id="loading-img" src="/assets/img/loading.gif" />

        <div class="overlay"></div>
        <div class="modal fade-scale" id="myModal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>

    </div>

    @include('layouts.foot')