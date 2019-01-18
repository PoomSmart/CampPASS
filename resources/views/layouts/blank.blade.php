@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 margin-tb">
            @yield('button')
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8">
            @yield('content')
        </div>
    </div>
@endsection