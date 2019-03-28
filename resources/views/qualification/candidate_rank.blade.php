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
                if (name.indexOf("checked") !== -1) {
                    url = "{!! route('qualification.form_check') !!}";
                    data = {
                        "form_score_id" : form_score_id,
                        "checked" : checked
                    };
                }
                else if (name.indexOf("passed") !== -1) {
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
                jQuery("input[name='score_threshold']").inputSpinner();
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
    @component('components.dialog', [
        'title' => trans('qualification.CandidatesAnnouncement'),
        'body' => trans('qualification.ContinueAnnounced'),
        'confirm_type' => 'danger',
        'confirm_label' => trans('app.Yes'),
    ])
    @endcomponent
    @php
        $i = $passed = 0;
    @endphp
    @if ($rank_by_score)
        <div class="d-flex align-items-center mb-2">
            <span class="mr-3">@lang('question.ScoreThreshold')</span>
            <form id="form" class="form-inline" method="POST" action="{{ route('questions.store', $camp->id) }}">
                @csrf
                @component('components.input', [
                    'name' => 'score_threshold',
                    'type' => 'number',
                    'placeholder' => trans('question.EnterThreshold'),
                    'no_form_control_class' => 1,
                    'attributes' => 'min=0.05 max=1.0 step=0.05 data-decimals=2',
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
            <th>@lang('qualification.Passed')</th>
            <th>@lang('qualification.Checked')</th>
            <th>@lang('app.Actions')</th>
        </thead>
        @foreach ($form_scores as $form_score)
            @php
                $registration = $form_score->registration;
                $camper = $registration->camper;
                $withdrawed = $registration->withdrawed();
                $returned = $registration->returned;
                $paid = $required_paid ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                if ($form_score->passed && !$returned)
                    ++$passed;
            @endphp
            <tr
                @if ($form_score->passed)
                    class="table-success"
                @elseif ($withdrawed || !$form_score->passed)
                    class="table-danger"
                @elseif ($returned || !$paid)
                    class="table-warning"
                @endif
            >
                <th scope="row">{{ ++$i }}</th>
                <th><a href="{{ route('profiles.show', $camper->id) }}" target="_blank">{{ $camper->getFullName() }}</a></th>
                @if ($rank_by_score)
                    <td class="fit">{{ $form_score->total_score }} / {{ $question_set->total_score }}</td>
                @else
                    <td>{{ $registration->submission_time }}</td>
                @endif
                <td>{{ $registration->getStatus() }}</td>
                @if ($required_paid)
                    <td class="text-center{{ $paid ? ' text-success table-success' : ' text-danger table-danger' }}">{{ $paid ? trans('app.Yes') : trans('app.No') }}</td>
                @endif
                <td class="text-center">
                    <input type="checkbox" name="passed_{{ $form_score->id }}" id="{{ $form_score->id }}"
                        @if ($withdrawed || !$paid)
                            disabled
                        @endif
                        @if ($form_score->passed)
                            checked
                        @endif
                    >
                </td>
                <td class="text-center">
                    <input type="checkbox" name="checked_{{ $form_score->id }}" id="{{ $form_score->id }}"
                        @if ($withdrawed || $returned)
                            disabled
                        @endif
                        @if ($form_score->checked)
                            checked
                        @endif
                    >
                </td>
                <td class="fit">
                    <a href="{{ route('qualification.show_profile_detailed', $registration->id) }}" target="_blank" class="btn btn-secondary"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewProfile')</a>
                    @role('admin')
                        @if (!$withdrawed)
                            <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">T Withdraw</a>
                            @if (!$returned)
                                <a href="{{ route('qualification.form_return', $registration->id) }}" class="btn btn-warning">T Return</a>
                            @endif
                        @endif
                    @endrole
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
        class="btn btn-danger w-50" {{ (!$passed || $question_set->candidate_announced) ? 'disabled' : null }}
        type="button"
        data-toggle="modal"
        data-target="#modal"
        data-action="{{ route('qualification.candidate_announce', $question_set->id) }}"
    ><i class="fas fa-bullhorn fa-xs mr-1"></i>@lang('qualification.Announce')</button>
@endsection