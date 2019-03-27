@extends('layouts.card')

@section('header')
    {{ $camp }}
@endsection

@section('card_content')
    @if (!$camp->approved)
        <div class="alert alert-warning text-center">
            <h3 class="mb-0">@lang('camp.Unapproved')</h3>
        </div>
    @endif
    <div class="row">
        <div class="col-12 text-center">
            <p><a target="_blank" href="{{ route('camps.by_category', $category->id) }}">{{ $category->getName() }}</a></p>
        </div>
        <div class="col-12 mb-2 text-center">
            <img class="img-fluid" style="height: 400px; max-height: 400px;" src="{{ $camp->getBannerPath($actual = false, $display = true) }}">
        </div>
        <div class="col-12 text-center mb-3">
            <p>{{ $camp->getShortDescription() }}</p>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang('camp.Details')</h4>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>@lang('camp.CampDate')</h5>
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
                @if ($camp->contact_campmaker)
                    <div class="col-12">
                        <h5>@lang('camp.CampMakerContactInfo')</h5>
                        <p class="text-muted">{{ $camp->contact_campmaker }}</p>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    @if (!$camp->approved)
                        @can('camp-approve')
                            <a class="btn btn-success w-100 mb-3" href="{{ route('camps.approve', $camp->id) }}"><i class="fas fa-check"></i>@lang('app.Approve')</a>
                        @endcan
                    @endif
                    @if (!auth()->user() || auth()->user()->isCamper())
                        @php
                            $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($camp);
                            $apply_text = $info['text'];
                            $disabled = $info['disabled'];
                            $route = $info['route'];
                        @endphp
                        <a id="apply" class="btn btn-primary w-100 mb-3{{ $disabled ? ' disabled' : ''}}"
                            href="{{ $route }}"
                        ><i class="far fa-file-alt fa-xs mr-2"></i>{{ $apply_text }}</a>
                    @endif
                    @if ($camp->url)
                        <a class="btn btn-secondary w-100 mb-3" target="_blank" href="{{ $camp->url }}"><i class="fas fa-external-link-alt fa-xs mr-2"></i>@lang('camp.URL')</a>
                    @endif
                    @if ($camp->fburl)
                        <a class="btn btn-fb w-100 mb-3" target="_blank" href="{{ $camp->fburl }}"><i class="fab fa-facebook fa-sm mr-2"></i>@lang('camp.FBURL')</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang('camp.Poster')</h4>
            <div class="text-center">
                <img class="img-fluid" src="{{ $camp->getPosterPath($actual = false, $display = true) }}">
            </div>
        </div>
        <div class="col-12">
            <p>{{ $camp->long_description }}</p>
        </div>
    </div>
    @if (sizeof($same_camps))
        <div class="row">
            @component('components.card_carousel', [
                'id' => 'sameCampsCarousel',
                'header' => trans('camp.CampHeldBy'),
                'objects' => $same_camps,
                'component' => 'components.camp_block',
            ])
            @endcomponent
            <script>
                jQuery(".next").click(function () { jQuery(this).closest(".carousel").carousel("next"); return false; });
                jQuery(".prev").click(function () { jQuery(this).closest(".carousel").carousel("prev"); return false; });
            </script>
        </div>
    @endif
@endsection