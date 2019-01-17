@extends('layouts.card')

@section('header')
    {{ trans('app.Login') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        @component('components.input', [
            'name' => 'identity',
            'type' => 'identity',
            'label' => trans('account.Username').' / '.trans('account.Email'),
            'attributes' => 'required autofocus',
        ])@endcomponent

        @component('components.input', [
            'name' => 'password',
            'label' => trans('account.Password'),
            'type' => 'password',
            'attributes' => 'required',
        ])@endcomponent

        <div class="form-group row">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        {{ trans('account.RememberMe') }}
                    </label>
                </div>
            </div>
        </div>
        @component('components.submit', ['label' => 'Submit'])
        @slot('postcontent')
            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ trans('account.ForgotYourPassword') }}
                </a>
            @endif
        @endslot
        @endcomponent
    </form>
@endsection
