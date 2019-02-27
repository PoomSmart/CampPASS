@extends('layouts.card')

@section('card_content')
    <div class="row justify-content-center">
        <h2>@lang('qualification.ThankCampApply')</h2>
    </div>
    <div class="row">
        <a class="btn btn-primary mx-auto" href="{{ route('camps.browser') }}">@lang('app.OK')</a>
    </div>
@endsection