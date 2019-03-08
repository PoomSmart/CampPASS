@extends('layouts.card')

@section('header')
    @lang('account.ResetPassword')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @component('components.input', [
            'name' => 'email',
            'label' => trans('account.Email'),
            'type' => 'email',
            'required' => 1,
        ])
        @endcomponent
        <div class="text-center mt-2">
            @component('components.submit', [
                'label' => trans('account.SendPasswordResetLink'),
            ])
            @endcomponent
        </div>
    </form>
@endsection
