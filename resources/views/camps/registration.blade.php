@extends('layouts.blank')

@section('script')
    <script src="{{ asset('js/status-popover.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    @include('components.status_popover')
@endsection

@section('header')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12">
@endsection

@section('content')
    @include('components.form_return_dialog')
    <div class="text-center">
        <p class="mb-0">{{ $category->getName() }}</p>
        @foreach ($camp->getTags() as $glyph => $tag)
            <label class="badge badge-secondary font-weight-normal"><i class="{{ $glyph }} mr-1 fa-xs"></i>{{ $tag }}</label>
        @endforeach
    </div>
    @php
        $question_set = $camp->question_set;
        $camp_procedure = $camp->camp_procedure;
        $rankable = $camp_procedure->candidate_required && !is_null($question_set);
        $required_paid = $camp->paymentOnly();
    @endphp
    <div class="row">
        @php
            $manual_grading_required = $question_set && $question_set->manual_required && !$question_set->candidate_announced;
            $candidate_required = $question_set && $camp_procedure->candidate_required;
        @endphp
        @if ($manual_grading_required)
            <div class="col-12 text-center">
                <b class="text-danger">@lang('qualification.ManualGradingRequired')</b>
            </div>
        @endif
        <div class="col-12">
            @if ($data && count($data))
                <h3>@lang('registration.Applicants') ({{ $total_registrations }})</h3>
                <table class="table table-striped">
                    <thead>
                        <th>@lang('registration.ID')</th>
                        <th>@lang('account.FullName')</th>
                        <th>@lang('account.School')</th>
                        <th>@lang('camper.Program')</th>
                        <th>@lang('registration.SubmissionTime')</th>
                        <th>@lang('registration.Status')
                            <i class="fas fa-info-circle ml-1 fa-xs" data-toggle="status-popover"></i></th>
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
                        <th>@lang('app.Actions')</th>
                    </thead>
                    @foreach ($data as $key => $registration)
                        @php
                            $camper = $registration->camper;
                            $approved = $registration->approved_to_confirmed();
                            $confirmed = $registration->confirmed();
                            $withdrawed = $registration->withdrawed();
                            $form_score = $registration->form_score;
                            $finalized = $form_score ? $form_score->finalized : false;
                            $paid = $required_paid ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                            $consent = $camp->parental_consent ? \App\Http\Controllers\CampApplicationController::get_consent_path($registration) : true;
                        @endphp
                        <tr
                            @if ($withdrawed)
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
                            <td class="fit text-center">{{ $registration->getStatus() }}</td>
                            @if ($required_paid)
                                @php $text_class = $paid ? $approved ? 'text-success' : 'text-secondary' : 'text-danger' @endphp
                                <td class="text-center {{ $text_class }}">
                                    @if ($paid)
                                        <a class="{{ $text_class }}"
                                            href="{{ route('camp_application.payment_download', $registration->id) }}"
                                            title=@lang('qualification.ViewPaymentSlip')
                                        >{{ $approved ? trans('app.Yes') : trans('qualification.SlipNotYetApproved') }}<i class="far fa-eye fa-xs ml-2"></i></a>
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
                                    <a target="_blank" class="{{ $finalized ? 'text-success ' : ' text-danger ' }}{{ (!$registration->submitted() && !auth()->user()->isAdmin()) ? ' disabled' : null }}"
                                            href="{{ route('qualification.form_grade', [
                                                'registration_id' => $registration->id,
                                                'question_set_id' => $question_set->id,
                                            ]) }}">{{ $finalized ? trans('app.Yes') : trans('app.No') }}<i class="far fa-eye fa-xs ml-2"></i></a>
                                </td>
                            @endif
                            <td class="fit">
                                @can('candidate-edit')
                                    @php
                                        $payment_exists = $has_payment && \App\Http\Controllers\CampApplicationController::get_payment_path($registration);
                                        $consent_exists = $has_consent && \App\Http\Controllers\CampApplicationController::get_consent_path($registration);
                                        $no_approved = ($has_payment && !$payment_exists) || ($has_consent && !$consent_exists);
                                    @endphp
                                    <button type="button"
                                        {{ $registration->approved() || $registration->returned ? 'disabled' : null }}
                                        class="btn btn-warning" title="{{ trans('qualification.ReturnFormFull') }}"
                                        data-action="{{ route('qualification.form_return', $registration->id) }}"
                                        data-toggle="modal"
                                        data-target="#return-modal"
                                    ><i class="fas fa-undo mr-1 fa-xs"></i>@lang('qualification.ReturnForm')</button>
                                    @if (!$confirmed && !$withdrawed)
                                        <a href="{{ route('qualification.document_approve', $registration->id) }}"
                                            class="btn btn-success {{ $registration->approved() || $no_approved || $registration->returned ? ' disabled' : null }}"
                                            title={{ trans('qualification.ApproveFormFull') }}
                                        ><i class="fas fa-check mr-1 fa-xs"></i>@lang('qualification.ApproveForm')</a>
                                    @endif
                                @endcan
                                @role('admin')
                                    @if (!$withdrawed)
                                        <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">TW</a>
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="d-flex justify-content-center">
                    {!! $data->links() !!}
                </div>
            @else
                <span class="text-muted">@lang('registration.EmptyRegistration')</span>
            @endif
        </div>
    </div>
@endsection

@if (isset($rankable) && $rankable && count($data))
    @section('extra-buttons')
        <a class="btn btn-warning w-50{{ $question_set->candidate_announced ? ' disabled' : null }}" href="{{ route('qualification.candidate_rank', $question_set->id) }}">@lang('qualification.Rank')</a>
    @endsection
@endif