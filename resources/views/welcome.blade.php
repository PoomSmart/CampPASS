@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="{{URL::asset('css/welcome.css')}}">
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="links">
                    <a href="">Camps</a>
                    <a href="">Applications</a>
                    <a href="">Profile</a>
                    <a href="">Statistics</a>
                    <a href="">Feedback</a>
                    <a href="">About Us</a>
                </div>
            </div>
        </div>
    </div>
@stop