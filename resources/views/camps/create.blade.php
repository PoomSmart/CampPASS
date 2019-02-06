@extends('layouts.card')
@include('camps.fields')

@section('header')
    @lang('camp.CreateCamp')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        @component('components.submit', ['label' => trans('app.Submit')])
        @endcomponent
    </form>
@endsection