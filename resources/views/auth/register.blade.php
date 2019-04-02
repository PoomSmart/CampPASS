@php
    if (empty($type))
        die("An internal error has occurred");
@endphp

@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='cgpa']").inputSpinner();
        });
    </script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

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
                'class' => 'btn btn-sucess w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection