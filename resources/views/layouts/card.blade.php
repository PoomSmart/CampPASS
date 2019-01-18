@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 margin-tb">
            @yield('button')
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8">
            <div class="card">
                <div class="card-header">@yield('header')</div>
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection