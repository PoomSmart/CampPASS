@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ trans('account.VerifyEmail') }}</div>

            <div class="card-body">
                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ trans('account.VerificationLinkSent') }}
                    </div>
                @endif

                {{ trans('account.VerifyCheckEmailBeforeProceed') }}
                {{ trans('account.IfNotReceiveEmail') }}, <a href="{{ route('verification.resend') }}">{{ trans('account.RequestVerifyLink') }}</a>.
            </div>
        </div>
    </div>
</div>
@endsection
