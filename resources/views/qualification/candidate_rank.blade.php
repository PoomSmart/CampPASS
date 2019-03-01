@extends('layouts.blank')

@section('script')
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@section('header')
    @lang('qualification.PassedCampers')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('content')
    @component('components.dialog', [
        'title' => trans('qualification.CandidatesAnnouncement'),
        'body' => 'Once the candidates are announced, you will no longer be able to make changes.
            The candidates will also be notified, and they have a right to withdraw until they explicitly confirmed the attendance. Continue?',
        'confirm_type' => 'danger',
        'confirm_label' => trans('app.Yes'),
    ])
    @endcomponent
    <div class="d-flex align-items-center mb-2">
        <span class="mr-3">@lang('question.ScoreThreshold')</span>
        <form id="form" class="form-inline" method="POST" action="{{ route('questions.store', $camp->id) }}">
            @csrf
            @component('components.numeric_range', [
                'name' => 'score_threshold',
                'placeholder' => trans('question.EnterThreshold'),
                'min' => 0.01,
                'max' => 1.0,
                'step' => 0.01,
                'object' => $question_set,
                'nowrapper' => 1,
                'input_class' => 'mr-3',
                'range_class' => 'mr-3',
            ])
            @endcomponent
            @component('components.submit', [
                'label' => trans('app.Save'),
            ])
            @endcomponent
        </form>
    </div>
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
            <th>@lang('qualification.Score')</th>
            <th>@lang('qualification.Passed')</th>
        </thead>
        @php
            $i = $passed = 0;
        @endphp
        @if (!$question_set->total_score)
            <p>Fatal error: Total score is zero.</p>
        @else
            @foreach ($form_scores as $form_score)
                @php
                    $registration = $form_score->registration;
                    $camper = $registration->camper;
                @endphp
                <tr>
                    <th scope="row">{{ ++$i }}</th>
                    <th><a href="{{ route('profiles.show', $camper->id) }}">{{ $camper->getFullName() }}</a></th>
                    <td>{{ $form_score->total_score }} / {{ $question_set->total_score }}</td>
                    @php
                        $camper_passed = $question_set->announced || ($camper_pass = $form_score->total_score / $question_set->total_score >= $question_set->score_threshold);
                    @endphp
                    <td class="text-center{{ $camper_passed ? ' table-success text-success' : ' table-danger text-danger' }}">{{ $camper_passed ? trans('app.Yes') : trans('app.No') }}</td>
                    @php if ($camper_passed) ++$passed; @endphp
                </tr>
            @endforeach
        @endif
    </table>
    <div class="d-flex justify-content-center">
        {!! $form_scores->links() !!}
    </div>
@endsection

@section('extra-buttons')
    <button class="btn btn-danger w-50" {{ (!$passed || $question_set->announced) ? 'disabled' : null }} type="button" data-toggle="modal" data-target="#modal" data-action="{{ route('qualification.candidate_announce', $question_set->id) }}">@lang('qualification.Announce')</button>
@endsection