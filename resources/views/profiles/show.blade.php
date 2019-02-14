@extends('layouts.card')

@section('header')
    {{ $user->getFullName() }}
@endsection

@if (\Auth::user() && \Auth::user()->id == $user->id)
    @section('extra-buttons')
        <a href="{{ route('profiles.edit', \Auth::user()->id) }}" class="btn btn-primary w-50">Update Profile</a>
    @endsection
@endif

@section('card_content')
    <div class="row mt-4">
        <div class="col-md-4 text-center">
            <img src="https://via.placeholder.com/150" alt="..." class="img-circle">
        </div>
        
        <div class="col-md-8"> 
            <h4 class="mb-4">@lang('account.Education')</h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>@lang('account.School')</h5>
                    <h6>{{ $user->school() }}</h6>
                </div>
                
                <div class="col-md-6">
                    <h5>@lang('camper.Program')</h5>
                    <h6>{{ $user->program() }}</h6>
                </div>
            </div>
            <h4 class="mb-4">@lang('camp.Camps')</h4>
            @php
                $camps = $user->belonging_camps();
            @endphp
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>Camper Since</h5>
                        <h6>{{ date_format(date_create($user->email_verified_at), 'j F Y') }}</h6>
                </div>
                <div class="col-md-6">
                    <h5>Camps Joined</h5>
                        <h6>{{ $camps->count() }}</h6>
                </div>
                <div class="col-12 mt-2">
                    <h5>Camp Activities</h5>
                        <div class="row">
                            @foreach ($camps->get() as $camp)
                                <div class="col-md-6">
                                    <a href="{{ route ('camps.show', $camp) }}">{{ $camp }}</a>
                                </div>
                            @endforeach
                        </div>
                </div>
            </div>
            {{-- Badges --}}
            @if (!empty($badges))
                <h4 class="mb-4">@lang('camp.Badges')</h4>
                <div class="row">
                    @foreach ($badges as $badge)
                        <div class="col-md-3 col-sm-3 col-xs-6 col-6 mb-2">
                            <img class="img-fluid" src={{ asset("/images/badges/{$badge->getImageName()}.png") }} alt="{{ $badge->badge_category()->name }}" title="{{ $badge->badge_category()->name }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection