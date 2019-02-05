@extends('layouts.blank')

@section('content')
    <div class="card">
        <div class="card-body">
            <h3>@yield('header')</h3>
            @yield('card_content')
        </div>
    </div>
@endsection