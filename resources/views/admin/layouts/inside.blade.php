<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->

        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <!-- Scripts -->
        <script>
            window.Laravel = {!! json_encode([
                    'csrfToken' => csrf_token(),
            ]) !!}
            ;
        </script>
        @yield('style')
    </head>
    <body class="theme-black">
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="preloader">
                    <div class="spinner-layer pl-red">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Please wait...</p>
            </div>
        </div>
        <div class="overlay"></div>
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                    <a href="javascript:void(0);" class="bars"></a>
                    <a class="navbar-brand" href="/">{{ config('app.name', 'Laravel') }}</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="pull-right">
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                                <i class="material-icons">input</i>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- #Top Bar -->
        <section>
            <aside id="leftsidebar" class="sidebar">
                <!-- User Info -->
                <div class="user-info">
                    <div class="image">
                        <img src="{{ Auth::user()->cover }}" width="48" height="48" alt="User Cover" />
                    </div>
                    <div class="info-container">
                        <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</div>
                        <div class="email"> {{ Auth::user()->email }} </div>
                    </div>
                </div>
                <!-- #User Info -->
                <!-- Menu -->
	<style type="text/css">
                    .ScrollStyle
                    {
                        max-height: 500px;
                        overflow-y: scroll;
                    }
                </style>
                <div class="ScrollStyle">
                <div class="menu">
                    <ul class="list">
                        <li class="header">Navigation</li>
                        <li class="{{(!isset($page) || $page == 'users') ? 'active' : ''}}">
                            <a href="{{ route('a:users') }}">
                                <i class="material-icons">face</i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="{{(isset($page) && $page == 'leagues') ? 'active' : ''}}">
                            <a href="{{ route('a:leagues') }}">
                                <i class="material-icons">stars</i>
                                <span>Leagues</span>
                            </a>
                        </li>
                        <li class="{{(isset($page) && $page == 'teams') ? 'active' : ''}}">
                            <a href="{{ route('a:teams') }}">
                                <i class="material-icons">supervisor_account</i>
                                <span>Teams</span>
                            </a>
                        </li>
                        <li class="{{(isset($page) && $page == 'schedules') ? 'active' : ''}}">
                            <a href="{{ route('a:schedules') }}">
                                <i class="material-icons">style</i>
                                <span>Schedule</span>
                            </a>
                        </li>
                        <li class="{{(isset($page) && $page == 'games') ? 'active' : ''}}">
                            <a href="{{ route('a:games') }}">
                                <i class="material-icons">golf_course</i>
                                <span>Games</span>
                            </a>
                        </li>
                        <li class="{{(isset($page) && $page == 'settings') ? 'active' : ''}}">
                            <a href="{{ route('a:settings') }}">
                                <i class="material-icons">settings</i>
                                <span>Reminders</span>
                            </a>
                        </li>
                    </ul>
                </div>
	 </div>
                <!-- #Menu -->
                <!-- Footer -->
                <div class="legal">
                    <div class="copyright">
                        &copy; {{date('Y')}} <a href="javascript:void(0);">{{ config('app.name', 'Laravel') }}</a>.
                    </div>
                    <div class="version">
                        <b>Version: </b> {{ config('app.version', '0.0.0') }}
                    </div>
                </div>
                <!-- #Footer -->
            </aside>
        </section>
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
        @yield('script')
    </body>
</html>
