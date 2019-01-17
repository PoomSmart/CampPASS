@extends('layouts.app')

@section('outer_content')
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
            @yield('button')
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">@yield('header')</div>
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection