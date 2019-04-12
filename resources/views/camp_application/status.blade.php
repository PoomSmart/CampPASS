@extends('layouts.blank')

@php
    $camp = $registration->camp;
    $camp_procedure = $camp->camp_procedure;
@endphp

@section('script')
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@section('header')
    @lang('registration.Status')
@endsection

@section('subheader')
    <a href="{{ route('camps.show', $camp->id) }}">{{ $camp }}</a>
@endsection

@section('custom-width')
    <div class="col-12 col-sm-8 col-lg-6 col-xl-5">
@endsection

@section('content')
    @component('components.dialog', [
        'body' => trans('status.WithdrawConfirm'),
        'confirm_label' => trans('app.Yes'),
        'confirm_type' => 'danger',
    ])
    @endcomponent
    @if ($registration->returned)
        @component('components.card', [
            'header' => trans('status.Application'),
            'override_header_color' => 'bg-danger text-white',
            'data' => [
                'button' => true,
            ],
        ])
        @slot('extra_body')
            @if ($registration->returned_reasons)
            @php $dict = \App\Http\Controllers\QualificationController::form_returned_reasons($camp->hasPayment()) @endphp
                <p class="card-text mb-0">@lang('registration.FormReturned')</p>
                <ul>
                    @foreach (json_decode($registration->returned_reasons) as $reason)
                        <li>{{ $dict[$reason] }}</li>
                    @endforeach
                </ul>
            @endif
        @endslot
        @slot('buttons')
            <a href="{{ route('camp_application.unreturn', $registration->id) }}" class="btn btn-warning w-100">@lang('registration.ConfirmCorrectionAndUnreturn')</a>
        @endslot
        @endcomponent
        @component('components.padding', [ 'height' => 80 ])@endcomponent
    @endif
    @if ($camp_procedure->candidate_required)
        @component('components.card', [
            'header' => trans('status.Application'),
            'data' => \App\Http\Controllers\CampApplicationController::statusDescription(1, $registration, $camp),
        ])
        @slot('buttons')
            <a href="{{ route('camp_application.prepare_questions_answers', $camp->id) }}" class="btn btn-warning w-100">@lang('registration.Edit')</a>
        @endslot
        @endcomponent
        @component('components.padding', [ 'height' => 80 ])@endcomponent
    @endif
    @if ($camp_procedure->interview_required)
        @component('components.card', [
            'header' => trans('status.Interview'),
            'data' => \App\Http\Controllers\CampApplicationController::statusDescription(2, $registration, $camp),
        ])
        @endcomponent
        @component('components.padding', [ 'height' => 80 ])@endcomponent
    @endif
    @if ($camp->hasPayment())
        @component('components.card', [
            'header' => $camp_procedure->deposit_required ? trans('camp.Deposit') : trans('camp.ApplicationFee'),
            'data' => \App\Http\Controllers\CampApplicationController::statusDescription(3, $registration, $camp, $camp_procedure),
        ])
        @slot('extra_body')
            <b>@lang('camp.PaymentInfo')</b>
            <p class="mb-0">{{ $camp->payment_information ? $camp->payment_information : trans('app.N/A') }}</p>
        @endslot
        @slot('buttons')
            <form id="form" name="form" class="w-100" action="{{ route('camp_application.payment_upload', $registration->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @component('components.file_upload', [
                    'args' => $registration->id,
                    'upload' => 1,
                    'download_route' => \App\Http\Controllers\CampApplicationController::get_payment_path($registration) ? 'camp_application.payment_download' : null,
                    'delete_route' => 'camp_application.payment_delete',
                    'auto_upload' => 1,
                    'full_width' => 1,
                    'name' => 'pdf',
                ])
                @endcomponent
            </form>
        @endslot
        @endcomponent
        @component('components.padding', [ 'height' => 80 ])@endcomponent
    @endif
    @if ($camp->parental_consent)
        @component('components.card', [
            'header' => trans('camp.ParentalConsent'),
            'data' => [
                'button' => $registration->chosen_to_confirmed(),
                'passed' => $registration->chosen_to_confirmed(),
            ],
        ])
        @slot('extra_body')
            <p class="card-text">@lang('camp.ParentalConsentInfo')</p>
            <a class="mb-2 font-weight-bold" href="{{ route('camps.attribute_download', [
                'camp' => $camp->id,
                'name' => 'parental_consent',
            ]) }}">@lang('registration.DownloadConsentFormHere')</a>
        @endslot
        @slot('buttons')
            <form id="form" name="form" class="w-100" action="{{ route('camp_application.consent_upload', $registration->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @component('components.file_upload', [
                    'args' => $registration->id,
                    'upload' => 1,
                    'download_route' => \App\Http\Controllers\CampApplicationController::get_consent_path($registration) ? 'camp_application.consent_download' : null,
                    'delete_route' => 'camp_application.consent_delete',
                    'auto_upload' => 1,
                    'full_width' => 1,
                    'name' => 'pdf',
                ])
                @endcomponent
            </form>
        @endslot
        @endcomponent
        @component('components.padding', [ 'height' => 80 ])@endcomponent
    @endif
    @component('components.card', [
        'header' => trans('status.Approval'),
        'data' => \App\Http\Controllers\CampApplicationController::statusDescription(4, $registration, $camp, $camp_procedure),
    ])
    @slot('buttons')
        <a href="{{ route('profiles.edit', auth()->user()->id) }}" class="btn btn-primary w-100"><i class="fas fa-pencil-alt mr-2 fa-xs"></i>@lang('profile.UpdateProfile')</a>
    @endslot
    @endcomponent
    @component('components.padding', [ 'height' => 80 ])@endcomponent
    @component('components.card', [
        'header' => trans('status.Confirmation'),
        'data' => \App\Http\Controllers\CampApplicationController::statusDescription(5, $registration, $camp, $camp_procedure),
    ])
    @slot('buttons')
        <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-primary w-100 mx-1">
            {{ $registration->confirmed() ? trans('app.Confirmed') : trans('app.Confirm') }}
        </a>
        <button type="button" data-toggle="modal" data-target="#modal" data-action="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger w-100 mx-1">
            {{ $registration->withdrawed() ? $registration->getStatus() : trans('registration.Withdraw') }}
        </button>
    @endslot
    @endcomponent
@endsection