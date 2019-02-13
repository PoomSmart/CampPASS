@extends('layouts.card')

@section('header')
    @lang('account.Name')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 text-center">
            <img src="https://via.placeholder.com/150" alt="..." class="img-circle">
        </div>
        
        <div class="col-md-8"> 
            <h3>Education</h3>
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>School</h5>
                    <h6>School A</h6>
                </div>
                
                <div class="col-md-6">
                    <h5>Program of Study</h5>
                    <h6>Math Sci</h6>
                </div>
            </div>
            <h3>Camp</h3>
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5>Camper Since</h5>
                        <h6>1 January 2019</h6>
                    <h5>Camp Activities</h5>
                        <h6>Camp A</h6>
                        <h6>Camp A</h6>
                        <h6>Camp A</h6>
                        <h6>Camp A</h6>
                </div>
                <div class="col-md-6">
                    <h5>Camp Joined</h5>
                    <h6>Math Sci</h6>
                </div>
            </div>
            {{-- Badge --}}
           <h3>Badge</h3>
                @foreach ($badges as $badge)
                     <img src={{ asset("/images/badges/{$badge->getImageName()}.png") }}>
                @endforeach
        </div>
    </div>       





        {{-- <div class="row">
            <div class="col-sm-4 text-left">
                    
            </div>
            
            <div class="col-sm-4">
                
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
                    @foreach ($badges as $badge)
                     <img src={{ asset("/images/badges/{$badge->getImageName()}.png") }}>
                    @endforeach
                </div> --}}

@endsection