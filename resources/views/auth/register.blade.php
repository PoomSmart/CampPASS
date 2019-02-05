<?php if (empty($type)) {
    die("An internal error has occurred");
}?>

@extends('layouts.card')
@include('auth.register-basic')

@section('header')
    {{ trans('account.Register') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input name="type" type="hidden" value="{{ $type }}">
        @yield('basic-fields')
        @if ($type == config('const.account.camper'))
            @include('auth.register-camper')
            @yield('camper-fields')
        @elseif ($type == config('const.account.campmaker'))
            
        @endif
        @component('components.submit', ['label' => trans('account.Register')])
        @endcomponent
    </form>
@endsection