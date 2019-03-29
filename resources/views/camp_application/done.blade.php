@extends('layouts.card')

@section('card_content')
    <div class="row justify-content-center">
        <h2>@lang('qualification.ThankCampApply')</h2>
    </div>
    <div class="row">
        <a class="btn btn-primary mx-auto" href="{{ route('camp_application.landing', $camp->id) }}"><i class="far fa-file-alt mr-2 fa-xs"></i>@lang('registration.Status')</a>
    </div>
@endsection