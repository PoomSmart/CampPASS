@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/question.js') }}"></script>
@endsection

@section('header')
    {{ trans('question.CreateQuestions') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('questions.store') }}">
        @csrf
        <input name="camp_id" id="camp_id" type="hidden" value="{{ $camp_id }}">
        @component('components.numeric_range', [
            'name' => 'score_threshold',
            'label' => trans('question.ScoreThreshold'),
            'placeholder' => trans('question.EnterThreshold'),
            'min' => 0.0,
            'max' => 1.0,
            'step' => 0.01,
            'object' => isset($object) ? $object : null,
        ])
        @endcomponent
        <div id="questions" class="mt-4">
            @component('questions.question', [
                'title' => 'Title',
                'label' => 'Question',
            ]);
            @endcomponent
        </div>
        <script>getInfo("{!! trans('question.AddMoreChoice') !!}", "{!! trans('question.AddMoreCheckbox') !!}");</script>
        @if (!empty($json))
            <script>
                var client_json = JSON.parse({!! $json !!});
                readJSON(client_json);
            </script>
        @endif
        @component('components.submit', ['label' => trans('app.Save')])
        @slot('postcontent')
            <button class="btn btn-success" type="button" onclick="addQuestion();"><span>{{ trans('question.AddMoreQuestion') }}</span></button>
        @endslot
        @endcomponent
    </form>
@endsection