<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/checkbox-require.js') }}"></script>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.1/css/flag-icon.css" integrity="sha256-9MbYWPlETRYDxprjQWUUAkiU6JtQaY1EustxQWdQwhk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <!-- Additional Styles -->
    @yield('style')
    <!-- Additional Scripts -->
    @yield('script')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}"><img class="img-fluid" style="width: 90px;" src={{ asset("/images/logo.png") }}></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="@lang('Toggle navigation')">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('camps.browser') }}">@lang('camp.BrowseCamps')</a>
                        </li>
                        @can('camp-create')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('camps.index') }}">@lang('camp.OrganizeCamps')</a>
                            </li>
                        @endcan
                        @if (\Auth::user() && \Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">@lang('account.ManageUsers')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('roles.index') }}">@lang('account.ManageRoles')</a>
                            </li>
                        @endif
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">@lang('app.Login')</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register-landing') }}">@lang('account.Register')</a>
                                </li>
                            @endif
                        @else
                            @role('camper')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('profiles.my_camps', \Auth::user()) }}">@lang('camper.MyCamps')</a>
                                </li>
                            @endrole
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ \Auth::user()->getFullName() }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profiles.index') }}">@lang('account.Profile')</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        @lang('app.Logout')
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                        <li class="nav-item my-auto">
                            @php $lang = app()->getLocale() == 'en' ? 'th' : 'en' @endphp
                            <a href="{{ route('locale', $lang) }}"><span class="flag-icon flag-icon-{{ $lang == 'en' ? 'us' : $lang }}"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        @if (View::hasSection('header'))
            <div class="container-fluid text-center" id="header">
                <h2 class="mb-0">@yield('header')</h2>
            </div>
        @endif

        <main class="py-4">
            <div class="container">
                @yield('outer_content')
            </div>
        </main>
        <footer class="footer">
            <div class="container pt-4">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <h6 class="text-left"><b>{{ config('app.name') }}</b></h6>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <ul class="list-unstyled">    
                                    <li><a href="#!">@lang ("app.What's") {{ config('app.name') }}</a></li>
                                    <li><a href="#!">@lang ('app.How') {{ config('app.name') }} @lang('app.works')</a></li>
                                    <li><a href="#!">@lang ('app.Becoming a Camp Maker')</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-6">
                                <ul class="list-unstyled">    
                                    <li><a href="#!">@lang ('app.About')</a></li>
                                    <li><a href="#!">@lang ('app.Help')</a></li>
                                    <li><a href="#!">@lang ('app.Terms')</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-unstyled list-inline social text-right">
                            <i class="fab fa-twitter"></i>
                            <i class="fab fa-facebook-f"></i>
                        </ul>
                        <button type="button" class="btn btn-outline-light float-right"><a href="https://www.camphub.in.th/">Go to CampHub.in.th <i class="fas fa-paper-plane"></i></a></button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
