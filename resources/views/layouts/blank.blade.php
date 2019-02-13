@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10">
            @yield('content')
        </div>
        @if (View::hasSection('extra-buttons'))
            <div class="col-sm-12 col-md-10 my-4 text-center">
                @yield('extra-buttons')
            </div>
        @endif
    </div>
@endsection