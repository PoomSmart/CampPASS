@extends('layouts.card')

@section('header')
    @lang('auth.WhoAreYou')
@endsection

@section('card_content')
    <h2><a href="{{ route('register-camper') }}">@lang('account.Camper')</a></h2>
    <h2><a href="{{ route('register-campmaker') }}">@lang('account.CampMaker')</a></h2>
@endsection