@extends('layouts.card')

@php
    $camp = $registration->camp();
    $camp_procedure = $camp->camp_procedure();
@endphp

@section('header')
    Application Status for {{ $camp }}
@endsection

@section('card_content')
    <div class="row mt-4">
        @if ($camp_procedure->candidate_required)
            <div class="col-md-4">
                Application Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Application</h4>
                    @if ($registration->applied())
                        <p>@lang('qualification.Grading')</p>
                    @elseif ($registration->approved() || $registration->qualified() || $registration->candidate())
                        <p>Congratulations, your application form has been accepted!</p>
                    @elseif ($registration->returned())
                        <p>Your application form has been returned, please check the completeness of the form and resubmit it.</p>
                    @else
                        <a href="{{ route('camp_application.landing', $camp->id) }}" class="btn btn-primary w-100">@lang('registration.Edit')</a>
                    @endif
            </div>
        @endif

        @if ($camp_procedure->interview_required)
            <div class="col-md-4">
                Interview Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Interview</h4>
                <p>Do acknowledge that you will be doing an interview.</p>
            </div>
        @endif

        @php
            $applied = $camp_procedure->candidate_required ? ($registration->applied() || $registration->returned()) : true;
            // TODO: Deal with the case interview_required = true
        @endphp
        @if ($camp_procedure->deposit_required && $applied)
            <div class="col-md-4">
                Deposit Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Deposit</h4>
                <p>Please upload your payment slip.</p>
                <a href="" class="btn btn-primary w-100">Upload Payment Slip</a>
            </div>
        @endif
        
        @if ($registration->approved_to_qualified())
            <div class="col-md-4">
                Qualification Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Qualification</h4>
                <p>Congratulations! Please do acknowledge your attendance by clicking confirm.</p>
                <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-primary w-100{{ $registration->qualified() ? ' disabled' : null }}">{{ $registration->qualified() ? trans('app.Confirmed') : trans('app.Confirm') }}</a>
            </div>
        @endif
    </div>
@endsection