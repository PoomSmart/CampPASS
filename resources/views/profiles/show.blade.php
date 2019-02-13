@extends('layouts.card')

@section('header')
    {{ \Auth::user()->getFullName() }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 text-center">
            <img src="https://via.placeholder.com/150" alt="..." class="img-circle">
        </div>
        
        <div class="col-md-8"> 
            <h4 class="mb-4">Education</h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>School</h5>
                    <h6>{{ $user->school() }}</h6>
                </div>
                
                <div class="col-md-6">
                    <h5>Program of Study</h5>
                    <h6>Math Sci</h6>
                </div>
            </div>
            <h4 class="mb-4">Camps</h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>Camper Since</h5>
                        <h6>1 January 2019</h6>
                </div>
                <div class="col-md-6">
                    <h5>Camps Joined</h5>
                    <h6>4</h6>
                </div>
                <div class="col-12 mt-2">
                    <h5>Camp Activities</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <span>Camp A</span>
                            </div>
                            <div class="col-md-6">
                                <span>Camp A</span>
                            </div>
                            <div class="col-md-6">
                                <span>Camp A</span>
                            </div>
                            <div class="col-md-6">
                                <span>Camp A</span>
                            </div>
                            <div class="col-md-6">
                                <span>Camp A</span>
                            </div>
                        </div>
                </div>
            </div>
            {{-- Badges --}}
           <h4 class="mb-4">Badges</h4>
            <div class="row">
                @foreach ($badges as $badge)
                    <div class="col-md-3 col-sm-3 col-xs-6 col-6 mb-2">
                        <img class="img-fluid" src={{ asset("/images/badges/{$badge->getImageName()}.png") }} alt="{{ $badge->badge_category()->name }}" title="{{ $badge->badge_category()->name }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection