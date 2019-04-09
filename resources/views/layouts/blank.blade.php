@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        @if (View::hasSection('extra-buttons-top'))
            <div class="col-12 my-4 text-center">
                @yield('extra-buttons-top')
            </div>
        @endif
        @if (View::hasSection('custom-width'))
            @yield('custom-width')
        @else
            <div class="col-12 col-sm-10 col-lg-8 col-xl-7">
        @endif
                @include('components.errors')
                @yield('content')
            </div>
        @if (!isset($card) && View::hasSection('extra-buttons'))
            <div class="col-12 my-4 text-center">
                @yield('extra-buttons')
            </div>
        @endif
    </div>
@endsection