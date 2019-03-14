@extends('layouts.card')

@section('header')
    {{ $camp }}
@endsection

@section('card_content')
    <div class="row">
        <div class="col-12 text-center">
            <p>{{ $category->getName() }}</p>
        </div>
        <div class="col-12 mb-2 text-center">
            <img class="img-fluid" src="http://placehold.it/880x600/{{ \App\Common::randomString(6) }}">
        </div>
        <div class="col-12 text-center mb-3">
            <p>{{ $camp->getShortDescription() }}</p>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang ('camp.Details')</h4>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>@lang ('camp.CampDate')</h5>
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
                    <p class="text-muted">{{ $camp->getAcceptableYears() }}</p>
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
                    <h5>@lang('organization.Organization')</h5>
                    <p class="text-muted">{{ $camp->organization }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @if (!$camp->approved)
                        @can('camp-approve')
                            <a class="btn btn-warning w-100 mb-3" href="{{ route('camps.approve', $camp->id) }}">@lang('app.Approve')</a>
                        @endcan
                    @endif
                    @php
                        $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($camp);
                        $apply_text = $info['text'];
                        $disabled = $info['disabled'];
                        $route = $info['route'];
                    @endphp
                    <a class="btn btn-primary w-100 mb-3{{ $disabled ? ' disabled' : ''}}"
                        href="{{ $route }}"
                    >{{ $apply_text }}</a>
                    @if ($camp->url)
                        <a class="btn btn-secondary w-100 mb-3" target="_blank" href="{{ $camp->url }}">@lang('camp.URL')</a>
                    @endif
                    @if ($camp->fburl)
                        <a class="btn btn-secondary w-100 mb-3" target="_blank" href="{{ $camp->fburl }}">@lang('camp.FBURL')</a>
                    @endif
                </div>
                <div class="col-12">
                    <p>{{ $camp->long_description }}</p>
                </div>
                @if ($camp->contact_campmaker)
                    <div class="col-12">
                        <h5>@lang('camp.CampMakerContactInfo')</h5>
                        <p class="text-muted">{{ $camp->contact_campmaker }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang ('camp.Poster')</h4>
        </div>
    </div>
@endsection