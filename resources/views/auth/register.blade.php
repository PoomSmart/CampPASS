@php if (empty($type)) {
    die("An internal error has occurred");
}@endphp

@extends('layouts.card')

@section('header')
    @lang('account.Register')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input name="type" type="hidden" value="{{ $type }}">
        @include('profiles.fields')
        <div class="mt-4 text-center">
            @component('components.submit', [
                'label' => trans('account.Register'),
                'attributes' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection