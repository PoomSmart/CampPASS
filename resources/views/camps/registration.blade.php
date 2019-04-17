@extends('layouts.blank')

@section('script')
    <script src="{{ asset('js/status-popover.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    @include('components.qualification.status_popover')
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12">
@endsection

@section('content')
    @include('components.qualification.form_return_dialog')
    @include('components.no_revert_dialog')
    <div class="text-center">
        <p class="mb-0">{{ $category->getName() }}</p>
        @foreach ($camp->getTags() as $glyph => $tag)
            <label class="badge badge-secondary font-weight-normal"><i class="{{ $glyph }} mr-1 fa-xs"></i>{{ $tag }}</label>
        @endforeach
    </div>
    @php
        $question_set = $camp->question_set;
        $camp_procedure = $camp->camp_procedure;
        $candidate_required = $question_set && $camp_procedure->candidate_required;
        $rank_by_score = $candidate_required && $question_set->total_score;
        $required_paid = $camp->paymentOnly();
        $manual_grading_required = $question_set && $question_set->manual_required;
    @endphp
    <div class="row">
        @if ($manual_grading_required)
            <div class="col-12 text-center">
                <b class="text-danger">@lang('qualification.ManualGradingRequired')</b>
            </div>
        @endif
        <div class="col-12">
            @if ($data && count($data))
                <h3>@lang('registration.Applicants') ({{ $total_registrations }})</h3>
                <form id="form" method="POST" action="{{ route('qualification.document_approve_save', $camp->id) }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <th>@lang('registration.ID')</th>
                            <th>@lang('account.FullName')</th>
                            <th>@lang('account.School')</th>
                            <th>@lang('camper.Program')</th>
                            <th>@lang('registration.SubmissionTime')</th>
                            <th>@lang('registration.Status')<i class="fas fa-info-circle ml-1 fa-xs" tabindex="0" style="cursor: pointer;" data-toggle="status-popover" data-trigger="focus"></i></th>
                            @if ($required_paid)
                                @if ($camp->application_fee)
                                    <th>@lang('qualification.ApplicationFeePaid')</th>
                                @else
                                    <th>@lang('qualification.DepositPaid')</th>
                                @endif
                            @endif
                            @if ($camp->parental_consent)
                                <th>@lang('qualification.ConsentUploaded')</th>
                            @endif
                            @if ($candidate_required)
                                <th>@lang('qualification.Finalized')</th>
                            @endif
                            <th>@lang('qualification.Checked')</th>
                            <th>@lang('app.Actions')</th>
                        </thead>
                        @foreach ($data as $key => $registration)
                            @php
                                $camper = $registration->camper;
                                $form_score = $registration->form_score;
                                $approved = $registration->approved_to_confirmed() || ($form_score && $form_score->checked);
                                $confirmed = $registration->confirmed();
                                $withdrawn = $registration->withdrawn();
                                $rejected = $registration->rejected();
                                $returned = $registration->returned;
                                $finalized = $form_score ? $form_score->finalized : false;
                                $paid = $required_paid ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                                $consent = $camp->parental_consent ? \App\Http\Controllers\CampApplicationController::get_consent_path($registration) : true;
                            @endphp
                            <tr
                                @if ($withdrawn || $rejected)
                                    class="table-danger"
                                @elseif ($confirmed)
                                    class="table-success"
                                @endif
                            >
                                <th scope="row">{{ $registration->id }}</th>
                                <th><a href="{{ route('qualification.show_profile_detailed', $registration->id) }}">{{ $camper->getFullName() }}</a></th>
                                <td class="text-truncate text-truncate-200" title="{{ $camper->school }}">{{ $camper->school }}</td>
                                <td>{{ $camper->program }}</td>
                                <td>{{ $registration->getSubmissionTime() }}</td>
                                <td class="fit text-center">
                                    @include('components.qualification.registration_status_cell', [ 'registration' => $registration ])
                                </td>
                                @if ($required_paid)
                                    @php $text_class = $paid ? $approved ? 'text-success' : 'text-secondary' : 'text-danger' @endphp
                                    <td class="text-center {{ $text_class }}">
                                        @if ($paid)
                                            <a class="{{ $text_class }}"
                                                href="{{ route('camp_application.payment_download', $registration->id) }}"
                                                title=@lang('qualification.ViewPaymentSlip')
                                            >{{ $approved ? trans('app.Yes') : trans('qualification.SlipNotYetApproved') }}<i class="fas fa-search-dollar fa-sm ml-2"></i></a>
                                        @else
                                            @lang('app.No')
                                        @endif
                                    </td>
                                @endif
                                @if ($camp->parental_consent)
                                    <td class="text-center{{ $consent ? ' text-success' : ' text-danger' }}">
                                        @if ($consent)
                                            <a class="text-success"
                                                href="{{ route('camp_application.consent_download', $registration->id) }}"
                                                title=@lang('qualification.ViewConsentForm')
                                            >@lang('app.Yes')<i class="far fa-eye fa-xs ml-2"></i></a>
                                        @else
                                            @lang('app.No')
                                        @endif
                                    </td>
                                @endif
                                @if ($candidate_required)
                                    <td class="text-center">
                                        <a class="{{ $finalized ? 'text-success ' : ' text-danger ' }}"
                                                href="{{ route('qualification.form_grade', [
                                                    'registration_id' => $registration->id,
                                                    'question_set_id' => $question_set->id,
                                                ]) }}">{{ $finalized ? trans('app.Yes') : trans('app.No') }}<i class="far fa-eye fa-xs ml-2"></i></a>
                                    </td>
                                @endif
                                <td class="text-center">
                                    <input type="checkbox" name="{{ $registration->id }}"
                                        {{-- TODO: Camp makers won't be allowed to revert the ticking of document approval, for now ($approved) --}}
                                        @if (!$paid || !$consent || $registration->returned || $withdrawn || $rejected || $approved)
                                            disabled
                                        @endif
                                        @if ($approved)
                                            checked
                                        @endif
                                    >
                                </td>
                                <td class="fit">
                                    @include('components.qualification.applicant_actions', [
                                        'registration' => $registration,
                                        'approved' => $approved,
                                        'returned' => $returned,
                                        'withdrawn' => $withdrawn,
                                        'rejected' => $rejected,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="d-flex justify-content-center">
                        {!! $data->links() !!}
                    </div>
                    <div class="text-center">
                        @component('components.submit', [
                            'label' => trans('app.SaveChanges'),
                            'glyph' => 'far fa-save fa-xs',
                            'auto_width' => 1,
                        ])
                        @endcomponent
                    </div>
                </form>
            @else
                <span class="text-muted">@lang('registration.EmptyRegistration')</span>
            @endif
        </div>
    </div>
@endsection

@if ($candidate_required && count($data))
    @section('extra-buttons')
        @component('components.a', [
            'class' => 'btn btn-warning',
            'href' => route('qualification.candidate_rank', $question_set->id),
            'glyph' => 'fas fa-sort-amount-up fa-xs',
            'label' => trans('qualification.Rank'),
            'auto_width' => 1,
        ])
        @endcomponent
    @endsection
@endif