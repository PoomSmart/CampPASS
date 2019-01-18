@extends('layouts.card')
@include('camps.fields')

@section('header')
    {{ trans('app.Edit') .' '. $object->getName() }}
@endsection

@section('button')
    <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
@endsection

@section('card_content')
    <form action="{{ route('camps.update', $object) }}" method="POST">
        @csrf
        @method('PUT')
        @yield('camp-fields')
        @component('components.submit', ['label' => 'Update'])
        @endcomponent
    </form>
@endsection