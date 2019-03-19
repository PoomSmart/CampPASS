@extends('layouts.card')

@section('header')
    @lang('qualification.ExportAllDocument')
@endsection

@section('card_content')
    <p>@lang('qualification.DataExportInfo')</p>
    <h3>@lang('app.CampApplicationForm')</h3>
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
                <input type="checkbox" name="checked[]">
            </div>
            <div class="col-8 text-left">
                <p>@lang('qualification.AllPaymentSlip')</p>
            </div>
        @endif
        @if ($camp_procedure->candidate_required)
            <div class="col-4 text-center mb-3">
                <input type="checkbox" name="checked[]">
            </div>
            <div class="col-8 text-left">
                <p>@lang('qualification.SubmittedApplicationForms')</p>
            </div>
            <div class="col-4 text-center mb-3">
                <input type="checkbox" name="checked[]">
            </div>
            <div class="col-8 text-left">
                <p>@lang('qualification.ParentConsentForms')</p>
            </div>
        @endif
    </div>
    <h3>@lang('profile.StudentDocuments')</h3>
    <div class="row">
        <div class="col-4 text-center mb-3">
            <input type="checkbox" name="checked[]">
        </div>
        <div class="col-8 text-left">
            <p>@lang('profile.Transcript')</p>
        </div>
        <div class="col-4 text-center mb-3">
            <input type="checkbox" name="checked[]">
        </div>
        <div class="col-8 text-left">
            <p>@lang('profile.StudentCertificate')</p>
        </div>
    </div>
    <button class="btn download-btn w-100"><i class="fa fa-download mr-1"></i>@lang('app.Download')</button>
@endsection