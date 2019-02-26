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
        @php
            $application_form_accepted = $camp_procedure->candidate_required ? ($registration->approved() || $registration->qualified() || $registration->candidate) : false;
        @endphp
        @if ($camp_procedure->candidate_required)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Application.png') }}" alt="Application" height="150" class="pb-3">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Application')</h4>
                    @if ($registration->applied())
                        <p>@lang('qualification.Grading')</p>
                    @elseif ($application_form_accepted)
                        <p>Congratulations, your application form has been accepted!</p>
                    @elseif ($registration->returned())
                        <p>Your application form has been returned, please check the completeness of the form and resubmit it.</p>
                    @else
                        <a href="{{ route('camp_application.landing', $camp->id) }}" class="btn btn-primary w-100 mb-4">@lang('registration.Edit')</a>
                    @endif
            </div>
        @endif

        @if ($camp_procedure->interview_required)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Interview.png') }}" alt="Interview" height="150" class="pb-3">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4"> @lang('status.Interview')</h4>
                @if ($application_form_accepted && $camp->interview_information)
                    <p>Interview Date: {{ $camp->getInterviewDate() }}</p>
                    <p>{{ $camp->interview_information }}</p>
                @else
                    <p>Do acknowledge that you will be doing an interview.</p>
                @endif
            </div>
        @endif

        @php
            // $applied = $camp_procedure->candidate_required ? ($registration->applied() || $registration->returned()) : true;
            // TODO: Deal with the case interview_required = true
            // Or, tell them here that they only need to upload payment slip if they pass the interview
        @endphp
        @if ($camp_procedure->deposit_required && $application_form_accepted)
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Deposit.png') }}" alt="Deposit" height="150" class="pb-3">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Deposit')</h4>
                <p>Please upload your payment slip.</p>
                <a href="" class="btn btn-primary w-100 mb-4">Upload Payment Slip</a>
            </div>
        @endif
        
        @if ($registration->approved_to_qualified())
            <div class="col-md-4">
                <img src="{{ asset('/images/placeholders/Status - Qualification.png') }}" alt="Qualification" height="150" class="pb-3">
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">@lang('status.Qualification')</h4>
                <p>Congratulations! Please do acknowledge your attendance by clicking confirm.</p>
                <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-primary w-100 mb-4{{ $registration->qualified() ? ' disabled' : null }}">{{ $registration->qualified() ? trans('app.Confirmed') : trans('app.Confirm') }}</a>
            </div>
        @endif
    </div>
@endsection