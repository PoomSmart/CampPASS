@extends('layouts.card')

@section('header')
    @lang('qualification.CandidateRanking') {{ $question_set->announced ? '(Announced)' : null }}
@endsection

@section('card_content')
    <p>Passing criteria: {{ $question_set->score_threshold * 100 }}%</p>
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
                    $passed = $question_set->announced || ($camper_pass = $form_score->total_score / $question_set->total_score >= $question_set->score_threshold);
                @endphp
                <td class="text-center{{ $passed ? ' table-success text-success' : ' table-danger text-danger' }}">{{ $passed ? trans('app.Yes') : trans('app.No') }}</td>
                @php if (isset($camper_pass) && $camper_pass) ++$passed; @endphp
            </tr>
        @endforeach
    </table>
@endsection

@section('extra-buttons')
    <!-- TODO: add confirmation -->
    <a class="btn btn-danger w-50{{ (!$passed || $question_set->announced) ? ' disabled' : '' }}" href="{{ route('qualification.candidate_announce', $question_set) }}">@lang('qualification.Announce')</a>
@endsection