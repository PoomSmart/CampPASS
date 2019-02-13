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
@endsection