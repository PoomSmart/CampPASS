@php if (empty($type)) {
    die("An internal error has occurred");
}@endphp

@extends('layouts.card')
@include('auth.register-basic')

@section('header')
    @lang('account.Register')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input name="type" type="hidden" value="{{ $type }}">
        @include('profiles.fields')
        @component('components.submit', [
            'label' => trans('account.Register'),
        ])
        @endcomponent
    </form>
@endsection