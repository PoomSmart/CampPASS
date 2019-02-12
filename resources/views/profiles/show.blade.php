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
                <h2 class="card-title">Education</h2>
                    <h4 class="card-text">School</h4>
                        <p class="card-text">Some example text some example text. John Doe is an architect and engineer</p>
                    
                    <h4 class="card-text">Camper Since</h4>
                        <p class="card-text">Some example text some example text. John Doe is an architect and engineer</p>
            </div>

            <div class="col-sm-4">
                <h2 class="card-title">Camp</h2>
                <h4 class="card-text">Program of Study</h4>
                    <p class="card-text">Some example text some example text. John Doe is an architect and engineer</p>
                <h4 class="card-text">Camp Activities</h4>
                    <p class="card-text">Some example text some example text. John Doe is an architect and engineer</p>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
            </div>
            <div class="col-8">
                    <h2 class="card-title">Badges</h2>
                    @foreach ($badges as $badge)
                     <img src={{ asset("/images/badges/{$badge->getImageName()}.png") }}>
                    @endforeach
                </div>

@endsection