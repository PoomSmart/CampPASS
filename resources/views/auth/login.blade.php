@extends('layouts.card')

@section('header')
    @lang('app.Login')
@endsection

@section('custom-width')
    <div class="col-sm-9 col-md-6 col-lg-5">
@endsection

@section('card_content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        @component('components.input', [
            'name' => 'identity',
            'type' => 'identity',
            'label' => trans('account.Username').' / '.trans('account.Email'),
            'required' => 1,
            'attributes' => 'autofocus',
        ])
        @endcomponent
        @component('components.input', [
            'name' => 'password',
            'label' => trans('account.Password'),
            'type' => 'password',
            'required' => 1,
        ])
        @endcomponent
        <div class="form-check form-check-inline mt-2">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">@lang('account.RememberMe')</label>
        </div>
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Login'),
            ])
            @endcomponent
            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">@lang('account.ForgotYourPassword')</a>
            @endif
        </div>
    </form>
@endsection
