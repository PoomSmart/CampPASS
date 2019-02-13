@extends('layouts.card')

@section('header')
    @lang('account.Name')
@endsection

    @section('content')
        <div class="row">
            <div class="col-sm-4 text-left">
                    <img src="https://via.placeholder.com/150" alt="..." class="img-circle">
            </div>
            
            <div class="col-sm-4">
                <h2>Education</h2>
                    <h4>School</h4>
                        <p>Some example text some example text. John Doe is an architect and engineer</p>
                    
                    <h4>Camper Since</h4>
                        <p>Some example text some example text. John Doe is an architect and engineer</p>
            </div>

            <div class="col-sm-4">
                <h2>Camp</h2>
                <h4>Program of Study</h4>
                    <p>Some example text some example text. John Doe is an architect and engineer</p>
                <h4>Camp Activities</h4>
                    <p>Some example text some example text. John Doe is an architect and engineer</p>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
            </div>
            <div class="col-8">
                <h2>Badges</h2>
                <div class="row">
                    @foreach ($badges as $badge)
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 col-6 mb-2">
                            <img class="img-fluid" src={{ asset("/images/badges/{$badge->getImageName()}.png") }} alt="{{ $badge->badge_category()->name }}" title="{{ $badge->badge_category()->name }}">
                        </div>
                    @endforeach
                </div>
            </div>
@endsection