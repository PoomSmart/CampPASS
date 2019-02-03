@extends('layouts.card')
@include('camps.fields', ['update' => 1])

@section('header')
    {{ trans('app.Edit') .' '. $object }}
@endsection

@section('card_content')
    <form action="{{ route('camps.update', $object) }}" method="POST">
        @csrf
        @method('PUT')
        @yield('camp-fields')
        @component('components.submit', ['label' => trans('app.Update')])
        @endcomponent
    </form>
@endsection