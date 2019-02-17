@extends('layouts.blank')

@section('header')
    @lang('xxx.Campers that passed') {{ $camp }} {{ $question_set->announced ? '(Announced)' : null }}
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-2">
        <span>Passing criteria: {{ $question_set->score_threshold * 100 }}%</span>
        <a class="btn btn-secondary" href="{{ route('questions.show', $camp->id) }}">@lang('question.EditScoreThreshold')</a>
    </div>
    <table class="table table-striped">
        <thead>
            <th class="align-middle">@lang('app.No_')</th>
            <th class="align-middle">@lang('account.FullName')</th>
            <th class="align-middle">@lang('qualification.Score')</th>
            <th class="align-middle">@lang('qualification.Passed')</th>
        </thead>
        @php
            $i = $passed = 0;
        @endphp
        @if (!$question_set->total_score)
            <p>Fatal error: Total score is zero.</p>
        @else
            @foreach ($form_scores as $form_score)
                @php
                    $registration = $form_score->registration();
                    $camper = $registration->camper();
                @endphp
                <tr>
                    <th class="align-middle" scope="row">{{ ++$i }}</th>
                    <th class="align-middle"><a href="{{ route('profiles.show', $camper) }}">{{ $camper->getFullName() }}</a></th>
                    <td class="align-middle">{{ $form_score->total_score }} / {{ $question_set->total_score }}</td>
                    @php
                        $camper_passed = $question_set->announced || ($camper_pass = $form_score->total_score / $question_set->total_score >= $question_set->score_threshold);
                    @endphp
                    <td class="text-center{{ $camper_passed ? ' table-success text-success' : ' table-danger text-danger' }}">{{ $camper_passed ? trans('app.Yes') : trans('app.No') }}</td>
                    @php if ($camper_passed) ++$passed; @endphp
                </tr>
            @endforeach
        @endif
    </table>
@endsection

@section('extra-buttons')
    <!-- TODO: add confirmation -->
    <a class="btn btn-danger w-50{{ (!$passed || $question_set->announced) ? ' disabled' : '' }}" href="{{ route('qualification.candidate_announce', $question_set->id) }}">@lang('qualification.Announce')</a>
@endsection