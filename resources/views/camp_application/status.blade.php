@extends('layouts.card')

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
    {{ $camp }}
@endsection

@section('card_content')
    @component('components.dialog', [
        'body' => 'If you withdrawed from this camp, you would no longer be able to apply for this camp. Are you sure you want to withdraw from this camp?',
        'confirm_label' => trans('app.Yes'),
        'confirm_type' => 'danger',
    ])
    @endcomponent
    <div class="row mt-4">
        @if ($camp_procedure->candidate_required)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Application.png') }}" alt="Application" class="pb-3 w-100">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Application')</h4>
                @if ($registration->returned)
                    <p>@lang ('qualification.ReturnedApplication')</p>
                @elseif ($registration->applied())
                    <p>@lang('qualification.Grading')</p>
                @elseif ($registration->chosen())
                    <p>@lang ('qualification.CongratulationsApp')</p>
                @else
                    <a href="{{ route('camp_application.prepare_questions_answers', $camp->id) }}" class="btn btn-primary w-100 mb-4">@lang('registration.Edit')</a>
                @endif
            </div>
        @endif

        @if ($camp_procedure->interview_required)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Interview.png') }}" alt="Interview" class="pb-3 w-100">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4"> @lang('status.Interview')</h4>
                @if ($registration->chosen())
                    @if ($registration->interviewed())
                        <p>@lang('qualification.CongratulationsInterview')</p>
                    @elseif ($camp->interview_information)
                        <p>@lang('camp.InterviewDate'): {{ $camp->getInterviewDate() }}</p>
                        <p>{{ $camp->interview_information }}</p>
                    @endif
                @elseif ($registration->withdrawed())
                    <p>@lang('qualification.Withdrawed')</p>
                @elseif ($registration->rejected())
                    <p>@lang('qualification.Rejected')</p>
                @else
                    <p>@lang('qualification.AckInterview')</p>
                @endif
            </div>
        @endif

        @if ($camp_procedure->deposit_required)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Deposit.png') }}" alt="Deposit" class="pb-3 w-100">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Deposit')</h4>
                @if ($registration->approved())
                    <p>@lang('registration.SlipApproved')</p>
                @else
                    @php $need_upload = false; @endphp
                    @if ($registration->rejected())
                        <p>@lang('qualification.Rejected')</p>
                    @elseif ($registration->paid())
                        @if ($registration->returned)
                            <p>@lang('registration.PleaseRecheckSlip')</p>
                            @php $need_upload = true; @endphp
                        @else
                            <p>@lang('registration.SlipUploaded')</p>
                        @endif
                    @elseif ($registration->chosen())
                        @php
                            $need_upload = true;
                            if ($camp_procedure->deposit_required) {
                                if ($camp_procedure->interview_required)
                                    $need_upload = $registration->interviewed();
                            }
                        @endphp
                    @endif
                    @if ($need_upload)
                        <p>@lang('registration.UploadPayment')</p>
                        <a href="" class="btn btn-primary w-100 mb-4">Upload Payment Slip</a>
                    @else
                        <p>@lang('registration.AckSlip')</p>
                    @endif
                @endif
            </div>
        @endif
        
        <div class="col-md-4">
            <img src="{{ asset('/images/placeholders/Status - Qualification.png') }}" alt="Qualification" class="pb-3 w-100">
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">@lang('status.Qualification')</h4>
            @if ($registration->approved_to_qualified())
                <p>@lang('qualification.AttendanceConfirm')</p>
            @else
                @php
                    $disable_confirm = true;
                @endphp
            @endif
            @php
                if (!$disable_confirm)
                    $disable_confirm = $registration->qualified() || $registration->withdrawed();
            @endphp
            <div class="d-flex">
                <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-primary w-50 mx-1 mb-4{{ $disable_confirm ? ' disabled' : null }}">
                    {{ $registration->qualified() ? trans('app.Confirmed') : trans('app.Confirm') }}
                </a>
                <button type="button" data-toggle="modal" data-target="#modal" data-action="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger w-50 mx-1 mb-4" {{ $registration->qualified() || $registration->withdrawed() ? 'disabled' : null }}>
                    {{ $registration->withdrawed() ? $registration->getStatus() : trans('registration.Withdraw') }}
                </button>
            </div>
        </div>
    </div>
@endsection