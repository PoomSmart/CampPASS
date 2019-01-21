@extends('layouts.card')

@section('header')
    {{ trans('account.ResetPassword') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @component('components.input', [
            'name' => 'email',
            'label' => trans('account.Email'),
            'type' => 'email',
            'attributes' => 'required',
        ])
        @endcomponent
        @component('components.submit', ['label' => 'account.SendPasswordResetLink'])
        @endcomponent
    </form>
@endsection
