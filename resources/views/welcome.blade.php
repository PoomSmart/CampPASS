@extends('layout.master')

@section('title', 'CampPASS')

@section('style')
<link rel="stylesheet" href="{{URL::asset('css/welcome.css')}}">
@stop

@section('content')
    <div class="links">
        <a href="">Camps</a>
        <a href="">Applications</a>
        <a href="">Profile</a>
        <a href="">Statistics</a>
        <a href="">Feedback</a>
        <a href="">About Us</a>
    </div>
@stop