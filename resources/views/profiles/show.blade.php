@extends('layouts.card')

@php
    $camper = $user->isCamper();
@endphp

@section('header')
    {{ $user->getFullName() }}
@endsection

@if (\Auth::user() && \Auth::user()->id == $user->id)
    @section('extra-buttons')
        <a href="{{ route('profiles.edit', \Auth::user()->id) }}" class="btn btn-primary w-50">@lang ('profile.UpdateProfile')</a>
    @endsection
@endif

@section('card_content')
    <div class="row mt-4">
        <div class="col-md-4 text-center">
            @php
                $alt = 'Profile Picture of '.$user->getFullName();
                $genders = [ 'M', 'F' ];
                $gender = $genders[$user->gender % 2];
            @endphp
            <img src="{{ asset("images/profiles/Profile_{$gender}.jpg") }}" alt="{{ $alt }}" title="{{ $alt }}" class="img-circle">
        </div>
        <div class="col-md-8"> 
            <h4 class="mb-4">@lang('account.Education')</h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    @if ($camper)
                        <h5>@lang('account.School')</h5>
                        <h6>{{ $user->school }}</h6>
                    @else
                        <h5>@lang('organization.Organization')</h5>
                        <h6>{{ $user->organization }}</h6>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>@lang('camper.Program')</h5>
                    <h6>{{ $user->program }}</h6>
                </div>
            </div>
            <h4 class="mb-4">@lang('camp.Camps')</h4>
            @php
                $camps = $user->getBelongingCamps($status = \App\Enums\ApplicationStatus::QUALIFIED);
            @endphp
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>
                        @if ($camper)
                            @lang('camper.CamperSince')
                        @else
                            @lang('campmaker.CampMakerSince')
                        @endif
                    </h5>
                        <h6>{{ strftime('%d %B %Y', date_create($user->email_verified_at)->getTimestamp()) }}</h6>
                </div>
                <div class="col-md-6">
                    <h5>
                        @if ($camper)
                            @lang('camper.CampsJoined')
                        @else
                            @lang('campmaker.CampsManaged')
                        @endif
                    </h5>
                        <h6>{{ $camps->count() }}</h6>
                </div>
                @if ($camps->count())
                    <div class="col-12 mt-2">
                        <h5>@lang('account.CampActivities')</h5>
                            <div class="row">
                                @foreach ($camps->get() as $camp)
                                    <div class="col-md-6">
                                        <a href="{{ route ('camps.show', $camp) }}">{{ $camp }}</a>
                                    </div>
                                @endforeach
                            </div>
                    </div>
                @endif
            </div>
            {{-- Badges --}}
            @if ($badges && $badges->isNotEmpty())
                <h4 class="mb-4">@lang('badge.Badges')</h4>
                <div class="row">
                    @foreach ($badges as $badge)
                        <div class="col-md-3 col-sm-3 col-xs-6 col-6 mb-2 my-auto">
                            <img class="img-fluid" src={{ asset("/images/badges/{$badge->getImageName()}.png") }} alt="{{ $badge->badge_category->name }}" title="{{ $badge->badge_category->name }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection