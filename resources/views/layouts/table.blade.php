@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="float-left">
                <h2>@yield('header')</h2>
            </div>
            <div class="float-right">
                @yield('button')
            </div>
        </div>
    </div>
    @yield('content')
@endsection