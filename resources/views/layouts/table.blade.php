@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="float-right">
                @yield('extra-buttons')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @yield('content')
        </div>
    </div>
@endsection