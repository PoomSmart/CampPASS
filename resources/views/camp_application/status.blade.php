@extends('layouts.card')

@php
    $camp = $registration->camp;
    $camp_procedure = $camp->camp_procedure;
@endphp

@section('header')
    @lang('registration.Status') - {{ $camp }}
@endsection

@section('card_content')
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
                    @if ($registration->interviewed)
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
                    @elseif ($registration->chosen())
                        <p>@lang('registration.AckSlip')</p>
                        @php $need_upload = true; @endphp
                    @elseif ($registration->paid())
                        @if ($registration->returned)
                            <p>@lang('registration.PleaseRecheckSlip')</p>
                            @php $need_upload = true; @endphp
                        @else
                            <p>@lang('registration.SlipUploaded')</p>
                        @endif
                    @endif
                    @if ($need_upload)
                        <p>@lang('registration.Please upload your payment slip.')</p>
                        <a href="" class="btn btn-primary w-100 mb-4">Upload Payment Slip</a>
                    @endif
                @endif
            </div>
        @endif
        
        @if ($registration->approved_to_qualified())
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Qualification.png') }}" alt="Qualification" class="pb-3 w-100">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Qualification')</h4>
                <p>@lang('qualification.AttendanceConfirm')</p>
                <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-primary w-100 mb-4{{ $registration->qualified() ? ' disabled' : null }}">{{ $registration->qualified() ? trans('app.Confirmed') : trans('app.Confirm') }}</a>
            </div>
        @endif
    </div>
@endsection