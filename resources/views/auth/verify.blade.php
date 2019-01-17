@extends('layouts.app')

@section('header')
{{ trans('account.VerifyEmail') }}
@endsection

@section('content')
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ trans('account.VerificationLinkSent') }}
        </div>
    @endif
    {{ trans('account.VerifyCheckEmailBeforeProceed') }}
    {{ trans('account.IfNotReceiveEmail') }}, <a href="{{ route('verification.resend') }}">{{ trans('account.RequestVerifyLink') }}</a>.
@endsection
