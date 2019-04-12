@extends('layouts.card')

@section('card_content')
    <div class="row text-center">
        <div class="col-12">
            <h2>@lang('qualification.ThankCampApply')</h2>
        </div>
        <div class="col-12">
            <p class="text-muted">{{ $camp }}</p>
        </div>
        <div class="col-12">
            <a class="btn btn-primary" href="{{ route('camp_application.status', $registration->id) }}"><i class="far fa-file-alt mr-2 fa-xs"></i>@lang('registration.Status')</a>
        </div>
    </div>
@endsection