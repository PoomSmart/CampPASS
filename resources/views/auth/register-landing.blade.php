@extends('layouts.card')

@section('header')
    @lang('auth.WhoAreYou')
@endsection

@section('card_content')
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="{{ asset('images/camper.png') }}" class="w-50" title="{{ trans('account.Camper') }}"/>
            <h2><a href="{{ route('register-camper') }}">@lang('account.Camper')</a></h2>
        </div>
        <div class="col-md-6 text-center">
            <img src="{{ asset('images/campmaker.png') }}" class="w-50" title="{{ trans('account.CampMaker') }}"/>
            <h2><a href="{{ route('register-campmaker') }}">@lang('account.CampMaker')</a></h2>
        </div>
    </div>
@endsection