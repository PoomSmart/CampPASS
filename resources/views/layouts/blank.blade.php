@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10 margin-tb">
            @yield('extra-buttons')
            <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('app.Back')</a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10">
            @yield('content')
        </div>
    </div>
@endsection