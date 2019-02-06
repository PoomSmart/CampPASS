@extends('layouts.card')

@section('header')
    @lang('auth.WhoAreYou')
@endsection

@section('card_content')
    <ul>
        <li><a href="{{ route('register-camper') }}">@lang('account.Camper')</a></li>
        <li><a href="{{ route('register-campmaker') }}">@lang('account.CampMaker')</a></li>
    </ul>
@endsection