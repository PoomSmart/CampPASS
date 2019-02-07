@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10 margin-tb">
            @yield('extra-buttons')
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10">
            @yield('content')
        </div>
    </div>
@endsection