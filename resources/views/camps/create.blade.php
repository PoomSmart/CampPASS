@extends('layouts.card')
@include('camps.fields')

@section('header')
    {{ trans('camp.CreateCamp') }}
@endsection

@section('button')
    <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        @component('components.submit', ['label' => 'Submit'])
        @endcomponent
    </form>
@endsection