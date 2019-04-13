<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'lang' => app()->getLocale(),
        ]); !!};
    </script>
    @if (!auth()->guest())
        <script>
            window.Laravel.userId = {!! auth()->user()->id; !!};
            window.Laravel.no_notification_text = "{!! trans('app.NoNotifications') !!}";
        </script>
    @endif
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/checkbox-require.js') }}"></script>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.1/css/flag-icon.css" integrity="sha256-9MbYWPlETRYDxprjQWUUAkiU6JtQaY1EustxQWdQwhk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <!-- Additional Styles -->
    @yield('style')
    @if (View::hasSection('datetime'))
        <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/th.js"></script>
        <script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
        <script src="{{ asset('js/datetime.js') }}"></script>
    @endif
    @can('camp-edit')
        <style>
            .popover-header {
                background: #3366cc;
            }

            .camppass-orange, .pagination a.active, .page-item.active .page-link {
                background-color: #3366cc;
            }

            a, .pagination a, .page-item.disabled .page-link, .pagination a:hover:not(.active) {
                color: #3366cc;
            }

            #card-border {
                border-color: #3366cc;
            }

            #side .nav-link.active, #side .nav-link:hover, #side .nav-link:focus {
                color: #111;
                background-color: lightblue;
            }
        </style>
    @endif
    <!-- Additional Scripts -->
    @yield('script')
</head>
<body
    @if (View::hasSection('sidebar-items'))
        data-spy="scroll" data-target="#side"
    @endif
>
    <div id="app" class="d-flex flex-column">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}"><img class="img-fluid" style="width: 90px;" src={{ asset("/images/logo.png") }}></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="@lang('Toggle navigation')">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('camps.browser') }}"><i class="fas fa-search fa-xs mr-2"></i>@lang('camp.BrowseCamps')</a>
                        </li>
                        @can('camp-create')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('camps.index') }}">@lang('camp.OrganizeCamps')</a>
                            </li>
                        @endcan
                        @if (auth()->user() && auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">@lang('account.ManageUsers')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('roles.index') }}">@lang('account.ManageRoles')</a>
                            </li>
                        @endif
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">@lang('app.Login')</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register-landing') }}"></i>@lang('account.Register')</a>
                                </li>
                            @endif
                        @else
                            @role('camper')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('profiles.my_camps', auth()->user()) }}">@lang('camper.MyCamps')</a>
                                </li>
                            @endrole
                            <li class="nav-item dropdown my-auto">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ auth()->user()->getFullName() }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if (!auth()->user()->isAdmin())
                                        <a class="dropdown-item" href="{{ route('profiles.index') }}">@lang('account.Profile')</a>
                                    @endif
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
                            <li class="nav-item dropdown my-auto">
                                <a class="nav-link dropdown-toggle" id="notifications" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell my-auto"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationsMenu" style="min-width: 35rem;">
                                    <div id="notificationsMenu"></div>
                                    <li class="nav-link text-center"><a href="{{ route('notifications.index') }}"><strong>@lang('notification.SeeAllNotifications')</strong></a></li>
                                </ul>
                            </li>
                        @endguest
                        <li class="nav-item my-auto">
                            @php $lang = app()->getLocale() == 'en' ? 'th' : 'en' @endphp
                            <a class="nav-link" href="{{ route('locale', $lang) }}"><span class="flag-icon flag-icon-{{ $lang == 'en' ? 'us' : $lang }}"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @if (View::hasSection('header'))
            <div class="container-fluid text-center camppass-orange" id="header">
                <h2 class="mb-0">@yield('header')
                    @if (View::hasSection('subheader'))
                     <span class="font-weight-normal">- @yield('subheader')</span>
                    @endif
                </h2>
            </div>
        @endif
        @if (View::hasSection('sidebar-items'))
            <div class="container-fluid position-relative" id="content">
                <div class="row justify-content-center h-100">
                    <aside class="col-md-2" id="left">
                        <div class="mt-5 mb-3 sticky-top" id="side">
                            <ul class="nav flex-md-column flex-row justify-content-between" id="sidenav">
                                @yield('sidebar-items')
                            </ul>
                        </div>
                    </aside>
                    <main class="col-md-10 py-4">
                        <div class="container-fluid">
                            @yield('outer_content')
                        </div>
                    </main>
                </div>
            </div>
        @else
            <main class="py-4">
                <div class="container-fluid">
                    @yield('outer_content')
                </div>
            </main>
        @endif
    </div>
    <footer class="footer camppass-orange">
        <div class="container pt-4">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h6 class="text-left"><b>{{ config('app.name') }}</b></h6>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <ul class="list-unstyled">
                                <li><a href="{{ route('what-is-camppass') }}">@lang("app.What's", ['entity' => config('app.name')])</a></li>
                                <li><a href="{{ route('how-camppass-works') }}">@lang('app.HowWork', ['entity' => config('app.name')])</a></li>
                                <li><a href="{{ route('register-campmaker') }}">@lang('app.Becoming a Camp Maker')</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <ul class="list-unstyled">
                                <li><a href="{{ route('about-us') }}">@lang('app.About')</a></li>
                                <li><a href="{{ route('terms-of-services') }}">@lang('app.Terms')</a></li>
                                <li><a href="{{ route('privacy-policy') }}">@lang('app.Policy')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <ul class="list-unstyled list-inline social text-right">
                        <a href="https://www.camphub.in.th/"><i class="fab fa-twitter ml-2"></i></a>
                        <a href="https://www.camphub.in.th/"><i class="fab fa-facebook-f ml-2"></i></a>
                    </ul>
                    <a class="btn btn-outline-light float-right" href="https://www.camphub.in.th/">@lang('app.GotoCampHUB') <i class="fas fa-paper-plane ml-1"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>