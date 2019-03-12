@extends('layouts.card')

@section('header')
    @lang("app.What's")
@endsection

@section('card_content')
    <div class="row mt-4">
        <div class="col-md-4">
            <img src="{{ asset('/images/logo.png') }}" alt="CampPASS" class="pb-3 w-100">
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">{{ config('app.name') }}</h4>
            <p>@lang('about.AboutCampPASS')</p>
        </div>
    </div>
@endsection