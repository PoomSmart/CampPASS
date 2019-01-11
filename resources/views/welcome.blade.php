@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="{{URL::asset('css/welcome.css')}}">
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="links">
            <a href="">Camps</a>
            @guest

            @else
                @if (Auth::user()->isCamper())
                    <a href="">Applications</a>
                    <a href="">Profile</a>
                @elseif (Auth::user()->isCampMaker())
                    <a href="">Qualification</a>
                    <a href="">Certificates</a>
                    <a href="{{ route('users.index') }}">Manage Users</a>
                    <a href="{{ route('roles.index') }}">Manage Roles</a>
                    <a href="{{ route('camps.index') }}">Manage Camps</a>
                @else

                @endif
                <a href="">Statistics</a>
                <a href="">Feedback</a>
            @endguest
            <a href="">About Us</a>
        </div>
    </div>
</div>
@stop