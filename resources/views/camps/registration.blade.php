@extends('layouts.blank')

@section('header')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12 col-lg-10">
@endsection

@section('content')
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
                <b class="text-info">** @lang('qualification.ManualGradingRequired') **</b>
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
                        <th class="fit">@lang('camper.Program')</th>
                        <th>@lang('registration.Status')</th>
                        @if ($required_paid)
                            @if ($camp->application_fee)
                                <th>@lang('qualification.ApplicationFeePaid')</th>
                            @else
                                <th>@lang('qualification.DepositPaid')</th>
                            @endif
                        @endif
                        @if ($candidate_required)
                            <th>@lang('qualification.Finalized')</th>
                        @endif
                        <th class="fit">@lang('app.Actions')</th>
                    </thead>
                    @foreach ($data as $key => $registration)
                        @php
                            $camper = $registration->camper;
                            $confirmed = $registration->confirmed();
                            $withdrawed = $registration->withdrawed();
                            $form_score = $registration->form_score;
                            $finalized = $form_score ? $form_score->finalized : false;
                            $paid = $required_paid ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                        @endphp
                        <tr
                            @if ($withdrawed)
                                class="table-danger"
                            @elseif ($confirmed)
                                class="table-success"
                            @endif
                        >
                            <th scope="row">{{ $registration->id }}</th>
                            <th><a href="{{ route('qualification.show_profile_detailed', $registration->id) }}" target="_blank">{{ $camper->getFullName() }}</a></th>
                            <td class="text-truncate text-truncate-400" title="{{ $camper->school }}">{{ $camper->school }}</td>
                            <td>{{ $camper->program }}</td>
                            <td class="fit text-center">{{ $registration->getStatus() }}</td>
                            @if ($required_paid)
                                <td class="text-center{{ $paid ? ' text-success' : ' text-danger' }}">{{ $paid ? trans('app.Yes') : trans('app.No') }}</td>
                            @endif
                            @if ($candidate_required)
                                <td class="text-center{{ $finalized ? ' text-success' : ' text-danger' }}">{{ $finalized ? trans('app.Yes') : trans('app.No') }}</td>
                            @endif
                            <td class="fit">
                                @if ($rankable)
                                    <a class="btn btn-secondary{{ (!$registration->submitted() && !auth()->user()->isAdmin()) ? ' disabled' : null }}"
                                        href="{{ route('qualification.form_grade', [
                                            'registration_id' => $registration->id,
                                            'question_set_id' => $question_set->id,
                                        ]) }}"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewForm')</a>
                                @endif
                                @role('admin')
                                    @if (!$withdrawed)
                                        <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">T Withdraw</a>
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