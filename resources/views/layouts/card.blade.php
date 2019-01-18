@extends('layouts.blank')

@section('content')
    <div class="card">
        <div class="card-header">@yield('header')</div>
        <div class="card-body">
            @yield('card_content')
        </div>
    </div>
@endsection