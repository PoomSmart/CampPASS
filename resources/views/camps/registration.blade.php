@extends('layouts.blank')

@section('header')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12">
@endsection

@section('content')
    <p class="text-center">{{ $category->getName() }} - {{ $camp->camp_procedure }}</p>
    @can('answer-list')
        @php
            $question_set = $camp->question_set;
            $rankable = $camp->camp_procedure->candidate_required && !is_null($question_set);
        @endphp
    @endcan
    <div class="row">
        @php
            $manual_grading_required = $question_set && $question_set->manual_required && !$question_set->announced;
            $candidate_required = $question_set && $question_set->camp->camp_procedure->candidate_required;
        @endphp
        @if ($manual_grading_required)
            <div class="col-12 text-center">
                <b class="text-info">** Manual Grading required. **</b>
            </div>
        @endif
        <div class="col-12">
            @if ($data && count($data))
                <h3>@lang('registration.ApplicationForms') ({{ $total_registrations }})</h3>
                <table class="table table-striped">
                    <thead>
                        <th>@lang('registration.ID')</th>
                        <th>@lang('account.FullName')</th>
                        <th>@lang('account.School')</th>
                        <th class="fit">@lang('camper.Program')</th>
                        <th>@lang('registration.Status')</th>
                        @if ($candidate_required)
                            <th>@lang('qualification.Finalized')</th>
                        @endif
                        <th class="fit">@lang('app.Actions')</th>
                    </thead>
                    @foreach ($data as $key => $registration)
                        @php
                            $camper = $registration->camper;
                        @endphp
                        <tr>
                            <th scope="row">{{ $registration->id }}</th>
                            <th><a href="{{ route('profiles.show', $camper->id) }}" target="_blank">{{ $camper->getFullName() }}</a></th>
                            <td>{{ $camper->school }}</td>
                            <td>{{ $camper->program }}</td>
                            <td class="fit text-center">{{ $registration->getStatus() }}</td>
                            @php
                                $form_score = $registration->form_score;
                                $finalized = $form_score ? $form_score->finalized : false;
                            @endphp
                            @if ($candidate_required)
                                <td class="text-center{{ $finalized ? ' text-success table-success' : ' text-danger table-danger' }}">{{ $finalized ? trans('app.Yes') : trans('app.No') }}</td>
                            @endif
                            <td class="fit">
                                @if ($rankable)
                                    <a class="btn btn-info{{ (!$registration->submitted() && !auth()->user()->isAdmin()) ? ' disabled' : null }}"
                                        href="{{ route('qualification.form_grade', [
                                            'registration_id' => $registration->id,
                                            'question_set_id' => $question_set->id,
                                        ]) }}">@lang('qualification.ViewForm')</a>
                                @endif
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
        <a class="btn btn-warning w-50{{ $question_set->announced ? ' disabled' : null }}" href="{{ route('qualification.candidate_rank', $question_set->id) }}">@lang('qualification.Rank')</a>
    @endsection
@endif