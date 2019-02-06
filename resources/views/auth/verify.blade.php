@extends('layouts.app')

@section('header')
    @lang('account.VerifyEmail')
@endsection

@section('content')
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            @lang('account.VerificationLinkSent')
        </div>
    @endif
    @lang('account.VerifyCheckEmailBeforeProceed')
    @lang('account.IfNotReceiveEmail'), <a href="{{ route('verification.resend') }}">@lang('account.RequestVerifyLink')</a>.
@endsection
