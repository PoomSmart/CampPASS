@extends('layouts.card')

@section('header')
    {{ trans('account.ResetPassword') }}
@endsection

<!--TODO: Simplify -->
@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ trans('account.Email') }}</label>
            <div class="col-md-6">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        @component('components.submit', ['label' => 'account.SendPasswordResetLink'])
        @endcomponent
    </form>
@endsection
