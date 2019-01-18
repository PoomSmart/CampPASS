@extends('layouts.card')

@section('header')
    {{ trans('auth.WhoAreYou') }}
@endsection

@section('card_content')
    <ul>
        <li><a href="{{ route('register-camper') }}">{{ trans('account.Camper') }}</a></li>
        <li><a href="{{ route('register-campmaker') }}">{{ trans('account.CampMaker') }}</a></li>
    </ul>
@endsection