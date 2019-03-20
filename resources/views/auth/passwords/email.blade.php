@extends('layouts.card')

@section('header')
    @lang('account.ResetPassword')
@endsection

@section('custom-width')
    <div class="col-12 col-sm-9 col-xl-6">
@endsection

@section('card_content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @component('components.input', [
            'name' => 'email',
            'label' => trans('account.Email'),
            'type' => 'email',
            'required' => 1,
            'placeholder' => trans('account.Email')
        ])
        @endcomponent
        <p class="text-center text-muted mb-0">@lang('account.ResetPasswordInfo')</p>
        <div class="text-center mt-2">
            @component('components.submit', [
                'label' => trans('account.SentPasswordLink'),
            ])
            @endcomponent
        </div>
    </form>
@endsection
