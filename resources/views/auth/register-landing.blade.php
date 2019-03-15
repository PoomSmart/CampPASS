@extends('layouts.blank')

@section('header')
    @lang('auth.WhoAreYou')
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 text-center">
        <div class="card card-with-shadow" id="{{ isset($border) ? 'card-border' : null }}">
                <a href="{{ route('register-camper') }}">
                    <img class="card-img-top w-50" src={{ asset('images/camper.png') }}>
                    <h4>@lang('account.Camper')</h4>
                </a>
        </div>
    </div>  
    <div class="col-md-6 text-center">
            <div class="card card-with-shadow" id="{{ isset($border) ? 'card-border' : null }}">
                <a href="{{ route('register-campmaker') }}">
                    <img class="card-img-top w-50" src={{ asset('images/CampMaker.png') }}>
                    <h4>@lang('account.CampMaker')</h4>
                </a>
            </div>
        </div>      
    </div>
@endsection