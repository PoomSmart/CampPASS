@extends('layouts.card')

@php
    $camp = $registration->camp();
    $camp_procedure = $camp->camp_procedure();
@endphp

@section('header')
    Application Status
@endsection

@section('card_content')
    <div class="row mt-4">
        @if ($camp_procedure->candidate_required)
            <div class="col-md-4">
                Application Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Application</h4>
                <p>
                    @if ($registration->applied())
                        @lang('qualification.Grading')
                    @elseif ($registration->approved() || $registration->qualified())
                        Congratulations, your application form has been accepted!
                    @elseif ($registration->returned())
                        Your application form has been returned, please check the completeness of the form and resubmit it.
                    @else
                        @lang('registration.Edit')
                    @endif
                </p>
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

        @if ($camp_procedure->deposit_required)
            <div class="col-md-4">
                Deposit Placeholder
            </div>
            <div class="col-md-8">
                <h4 class="mb-4">Deposit</h4>
                <p>Please upload your payment slip.</p>
                <a href="" class="btn btn-primary w-100">Upload Payment Slip</a>
            </div>
        @endif
        
        <div class="col-md-4">
            Qualified Placeholder
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">Qualified</h4>
            <p>Congratulations! Please do acknowledge your attendance by clicking confirm.</p>
            <a href="" class="btn btn-primary w-100">@lang('app.Confirm')</a>
        </div>
    </div>
@endsection