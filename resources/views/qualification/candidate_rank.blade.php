@extends('layouts.blank')

@section('script')
    @php
        $rank_by_score = $question_set->total_score;
        $required_paid = $camp->application_fee;
    @endphp
    <script src="{{ asset('js/modal.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input:checkbox").change(function () {
                var self = jQuery(this);
                var form_score_id = self.attr("id");
                var checked = self.is(":checked");
                var name = self.attr("name");
                var url = "";
                var data = null;
                if (name.indexOf("passed") !== -1) {
                    url = "{!! route('qualification.form_pass') !!}";
                    data = {
                        "form_score_id" : form_score_id,
                        "passed" : checked
                    };
                }
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    headers: { "X-CSRF-TOKEN": window.Laravel.csrfToken },
                    contentType: "json",
                    processData: false,
                    data: JSON.stringify(data),
                    success: function (data) {}
                });
            });
        });
    </script>
    @if ($rank_by_score)
        <script src="{{ asset('js/input-spinner.js') }}"></script>
        <script>
            jQuery(document).ready(function () {
                jQuery("input[name='minimum_score']").inputSpinner();
            });
        </script>
        <script src="{{ asset('js/check-unsaved.js') }}"></script>
    @endif
@endsection

@section('header')
    @lang('qualification.ChosenCampers')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12 col-md-9">
@endsection

@section('content')
    @include('components.form_return_dialog')
    @include('components.no_revert_dialog')
    @component('components.dialog', [
        'title' => trans('qualification.CandidatesAnnouncement'),
        'body' => trans('qualification.ContinueAnnounced'),
        'confirm_type' => 'danger',
        'confirm_label' => trans('app.Yes'),
        'id' => 'announce-modal',
    ])
    @endcomponent
    @php $i = $passed = 0 @endphp
    @if ($rank_by_score)
        <div class="d-flex align-items-center mb-2">
            <span class="mr-3">@lang('question.MinimumScore')</span>
            <form id="form" class="form-inline" method="POST" action="{{ route('questions.store', $camp->id) }}">
                @csrf
                @component('components.input', [
                    'name' => 'minimum_score',
                    'type' => 'number',
                    'placeholder' => trans('question.EnterMinimumScore'),
                    'no_form_control_class' => 1,
                    'attributes' => "min=0 max={$question_set->total_score} step=1",
                    'object' => $question_set,
                    'nowrapper' => 1,
                ])
                @endcomponent
                @component('components.submit', [
                    'label' => trans('app.Save'),
                    'class' => 'btn btn-primary ml-3',
                    'glyph' => 'far fa-save fa-xs',
                ])
                @endcomponent
            </form>
        </div>
    @endif
    <div class="d-flex">
        <span class="text-muted">{{ $summary }}</span>
    </div>
    <div class="d-flex justify-content-center">
        {!! $form_scores->links() !!}
    </div>
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('account.FullName')</th>
            @if ($rank_by_score)
                <th>@lang('qualification.Score')</th>
            @else
                <th>@lang('qualification.SubmissionTime')</th>
            @endif
            <th>@lang('registration.Status')</th>
            @if ($required_paid)
                <th>@lang('qualification.ApplicationFeePaid')</th>
            @endif
            @if ($camp->parental_consent)
                <th>@lang('qualification.ConsentUploaded')</th>
            @endif
            <th>@lang('qualification.Passed')</th>
            <th>@lang('app.Actions')</th>
        </thead>
        @foreach ($form_scores as $form_score)
            @php
                $registration = $form_score->registration;
                $camper = $registration->camper;
                $approved = $registration->approved_to_confirmed();
                $withdrawed = $registration->withdrawed();
                $rejected = $registration->rejected();
                $returned = $registration->returned;
                $paid = $required_paid ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                $consent = $camp->parental_consent ? \App\Http\Controllers\CampApplicationController::get_consent_path($registration) : true;
                $checked = $form_score->checked;
                if ($form_score->passed && !$returned)
                    ++$passed;
            @endphp
            <tr
                @if ($withdrawed || !$form_score->passed)
                    class="table-danger"
                @elseif ($returned || !$paid || !$consent)
                    class="table-warning"
                @elseif ($form_score->passed)
                    class="table-success"
                @endif
            >
                <th scope="row">{{ ++$i }}</th>
                <th><a href="{{ route('qualification.show_profile_detailed', $registration->id) }}">{{ $camper->getFullName() }}</a></th>
                @if ($rank_by_score)
                    <td class="fit">{{ $form_score->total_score }} / {{ $question_set->total_score }}</td>
                @else
                    <td>{{ $registration->getSubmissionTime() }}</td>
                @endif
                <td>{{ $registration->getStatus() }}</td>
                @if ($required_paid)
                    @php $text_class = $paid ? ($approved || $checked) ? 'text-success' : 'text-secondary' : 'text-danger' @endphp
                    <td class="text-center {{ $text_class }}">
                        @if ($paid)
                            <a class="{{ $text_class }}{{ $withdrawed ? ' btn disabled' : '' }}"
                                @if (!$withdrawed)
                                    href="{{ route('camp_application.payment_download', $registration->id) }}"
                                @endif
                                title=@lang('qualification.ViewPaymentSlip')
                            >{{ ($approved || $checked) ? trans('app.Yes') : trans('qualification.SlipNotYetApproved') }}<i class="far fa-eye fa-xs ml-2"></i></a>
                        @else
                            @lang('app.No')
                        @endif
                    </td>
                @endif
                @if ($camp->parental_consent)
                    <td class="text-center{{ $consent ? ' text-success' : ' text-danger' }}">
                        @if ($consent)
                            <a class="text-success" href="{{ route('camp_application.consent_download', $registration->id) }}" title=@lang('qualification.ViewConsentForm')>
                                @lang('app.Yes')<i class="far fa-eye fa-xs ml-1"></i>
                            </a>
                        @else
                            @lang('app.No')
                        @endif
                    </td>
                @endif
                <td class="text-center">
                    <input type="checkbox" name="passed_{{ $form_score->id }}" id="{{ $form_score->id }}"
                        @if ($withdrawed)
                            disabled
                        @endif
                        @if ($form_score->passed)
                            checked
                        @endif
                    >
                </td>
                <td class="fit">
                    @include('components.applicant_actions', [
                        'registration' => $registration,
                        'approved' => $approved,
                        'returned' => $returned,
                        'withdrawed' => $withdrawed,
                        'rejected' => $rejected,
                    ])
                </td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $form_scores->links() !!}
    </div>
@endsection

@section('extra-buttons')
    <button
        class="btn btn-danger w-50"
        @if (!$passed)
            disabled
        @endif
        type="button"
        data-toggle="modal"
        data-target="#announce-modal"
        data-action="{{ route('qualification.candidate_announce', $question_set->id) }}"
    ><i class="fas fa-bullhorn fa-xs mr-1"></i>@lang('qualification.Announce')</button>
@endsection