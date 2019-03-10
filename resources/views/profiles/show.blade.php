@extends('layouts.card')

@php
    $camper = $user->isCamper();
@endphp

@section('header')
    {{ $user->getFullName() }}
@endsection

@if (auth()->user() && auth()->user()->id == $user->id)
    @section('extra-buttons')
        <a href="{{ route('profiles.edit', auth()->user()->id) }}" class="btn btn-primary w-50">@lang ('profile.UpdateProfile')</a>
    @endsection
@endif

@section('card_content')
    <div class="row mt-4">
        <div class="col-lg-4 text-center">
            <img src="{{ \App\Http\Controllers\ProfileController::profile_picture_path($user) }}" class="rounded-circle img-fluid w-75 p-2">
        </div>
        <div class="col-lg-8"> 
            <h4 class="mb-4">@lang('account.Education')</h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    @if ($camper)
                        <h5>@lang('account.School')</h5>
                        <p>{{ $user->school }}</p>
                    @else
                        <h5>@lang('organization.Organization')</h5>
                        <p>{{ $user->organization }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>@lang('camper.Program')</h5>
                    <p>{{ $user->program }}</p>
                </div>
            </div>
            <h4 class="mb-4">@lang('camp.Camps')</h4>
            @php
                $camps = $user->getBelongingCamps($status = \App\Enums\ApplicationStatus::CONFIRMED);
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
                        <p>{{ strftime('%d %B %Y', date_create($user->email_verified_at)->getTimestamp()) }}</p>
                </div>
                <div class="col-md-6">
                    <h5>
                        @if ($camper)
                            @lang('camper.CampsJoined')
                        @else
                            @lang('campmaker.CampsManaged')
                        @endif
                    </h5>
                        <p>{{ $camps->count() }}</p>
                </div>
                @if ($camps->count())
                    <div class="col-12 mt-2">
                        <h5>
                            @if ($camper)
                                @lang('camper.CamperActivities')
                            @else
                                @lang('campmaker.CampMakerActivities')
                            @endif
                        </h5>
                            <div class="row">
                                @foreach ($camps->get() as $camp)
                                    <div class="col-md-6">
                                        <a href="{{ route('camps.show', $camp) }}">{{ $camp }}</a>
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