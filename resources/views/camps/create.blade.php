@extends('layouts.card')
@include('camps.fields')

@section('header')
    {{ trans('camp.CreateCamp') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        @component('components.submit', ['label' => 'Submit'])
        @endcomponent
    </form>
@endsection