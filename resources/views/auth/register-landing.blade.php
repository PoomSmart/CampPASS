@extends('layouts.card')

@section('header')
    @lang('auth.WhoAreYou')
@endsection

@section('card_content')
    <div class="row">
        <div class="col-md-6 text-center">
            <a href="{{ route('register-camper') }}">
                <img src="{{ asset('images/camper.png') }}" class="w-50" title="{{ trans('account.Camper') }}"/>
                <h2>@lang('account.Camper')</h2>
            </a>
        </div>
        <div class="col-md-6 text-center">
            <a href="{{ route('register-campmaker') }}">
                <img src="{{ asset('images/campmaker.png') }}" class="w-50" title="{{ trans('account.CampMaker') }}"/>
                <h2>@lang('account.CampMaker')</h2>
            </a>
        </div>
    </div>
@endsection