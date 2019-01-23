@extends('layouts.card')

@section('card_content')
    <div class="row justify-content-center">
        <h2>Thank you for applying for the camp.</h2>
    </div>
    <div class="row">
        <a class="btn btn-primary mx-auto" href="{{ route('home') }}">{{ trans('app.OK') }}</a>
    </div>
@endsection