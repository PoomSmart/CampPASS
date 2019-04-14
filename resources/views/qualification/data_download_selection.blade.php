@extends('layouts.card')

@section('header')
    @lang('qualification.ExportAllDocument')
@endsection

@section('card_content')
    <form method="GET" action="{{ route('qualification.data_download', $question_set->id) }}">
        @csrf
        <h3 class="ml-2">@lang('app.CampApplicationForm')</h3>
        <div class="row">
            <div class="col-4 text-center mb-3">
                <h5>@lang('qualification.Checkbox')</h5>
            </div>
            <div class="col-8 text-left">
                <h5>@lang('qualification.Documents')</h5>
            </div>
        </div>
        <div class="row">
            @if ($camp->hasPayment())
                <div class="col-4 text-center mb-3">
                    <input type="checkbox" name="payment">
                </div>
                <div class="col-8 text-left">
                    <p>@lang('qualification.AllPaymentSlip')</p>
                </div>
            @endif
            @if ($camp_procedure->candidate_required)
                <div class="col-4 text-center mb-3">
                    <input type="checkbox" name="submitted-form">
                </div>
                <div class="col-8 text-left">
                    <p>@lang('qualification.SubmittedApplicationForms')</p>
                </div>
            @endif
            @if ($camp->parental_consent)
                <div class="col-4 text-center mb-3">
                    <input type="checkbox" name="consent-form">
                </div>
                <div class="col-8 text-left">
                    <p>@lang('qualification.ParentConsentForms')</p>
                </div>
            @endif
        </div>
        <h3 class="ml-2">@lang('profile.StudentDocuments')</h3>
        <div class="row">
            <div class="col-4 text-center mb-3">
                <input type="checkbox" name="transcript">
            </div>
            <div class="col-8 text-left">
                <p>@lang('profile.Transcript')</p>
            </div>
            <div class="col-4 text-center mb-3">
                <input type="checkbox" name="confirmation_letter">
            </div>
            <div class="col-8 text-left">
                <p>@lang('profile.StudentConfirmationLetter')</p>
            </div>
        </div>
        <h3 class="ml-2">@lang('account.Allergy')</h3>
        <div class="row">
            <div class="col-4 text-center mb-3">
                <input type="checkbox" name="allergy">
            </div>
            <div class="col-8 text-left">
                <p>@lang('account.Allergy')</p>
            </div>
        </div>
        @component('components.submit', [
            'label' => trans('app.Download'),
            'class' => 'btn btn-primary w-100',
            'glyph' => 'fa fa-download',
        ])
        @endcomponent
        <p class="text-center text-muted mb-0">@lang('qualification.DataExportInfo')</p>
    </form>
@endsection