@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>@yield('header')</h2>
            </div>
            <div class="pull-right">
                @yield('button')
            </div>
        </div>
    </div>
    @yield('content')
@endsection