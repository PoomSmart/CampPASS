@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="float-left">
                <h2>@yield('header')</h2>
            </div>
            <div class="float-right">
                @yield('extra-buttons')
                <a href="{{ url()->previous() }}" class="btn btn-default">{{ trans('app.Back') }}</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @yield('content')
        </div>
    </div>
@endsection