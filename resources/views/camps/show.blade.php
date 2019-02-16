@extends('layouts.card')

@section('header')
    {{ $camp }}
@endsection

@section('card_content')
    <div class="row">
        <div class="col-12">
            <p>{{ $category->getName() }} - {{ $camp->getShortDescription() }}</p>
        </div>
        <div class="col-12 mb-4">
            <img class="img-fluid" src="http://placehold.it/880x600/{{ \App\Common::randomString(6) }}">
        </div>
        <div class="col-12 col-md-6">
            <h4>Details</h4>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>Camp Date</h5>
                    <p class="text-muted">{{ $camp->getEventStartDate() }} -<br>{{ $camp->getEventEndDate() }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.AppCloseDate')</h5>
                    <p class="text-muted">{{ $camp->getCloseDate() }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.Quota')</h5>
                    <p class="text-muted">{{ $camp->quota ? $camp->quota : trans('camp.UnlimitedQuota') }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.CampFor')</h5>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.AcceptableProgramsShort')</h5>
                    <p class="text-muted">{{ $camp->getAcceptablePrograms() }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.MinCGPA')</h5>
                    <p class="text-muted">{{ $camp->min_cgpa ? $camp->min_cgpa : trans('app.Unspecified') }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.OtherConditions')</h5>
                    <p class="text-muted">{{ $camp->other_conditions ? $camp->other_conditions : trans('app.None') }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.ApplicationFee')</h5>
                    <p class="text-muted">{{ $camp->application_fee ? $camp->application_fee : trans('app.None') }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.CampLocation')</h5>
                    <p class="text-muted">Location X</p>
                </div>
                <div class="col-12 col-md-6">
                    <h5>@lang('account.CampMaker')</h5>
                    <p class="text-muted">{{ $camp->organization() }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @php
                        $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($camp);
                        $apply_text = $info['text'];
                        $disabled = $info['disabled'];
                    @endphp
                    <a class="btn btn-primary w-100 mb-3{{ $disabled ? ' disabled' : ''}}"
                        href="{{ route('camp_application.landing', $camp->id) }}"
                    >{{ $apply_text }}</a>
                    <a class="btn btn-secondary w-100 mb-3" target="_blank" href="{{ $camp->getURL() }}">@lang('camp.ContactCampMaker')</a>
                </div>
                <div class="col-12">
                    <p>{{ $camp->long_description }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <h4>Poster</h4>
        </div>
    </div>
@endsection