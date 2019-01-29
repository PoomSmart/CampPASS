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
        @component('components.input', [
            'name' => 'score_threshold',
            'label' => trans('camp.ScoreThreshold'),
            'type' => 'number',
            'placeholder' => trans('question.EnterThreshold'),
            'attributes' => 'step=any',
        ])@endcomponent
        <div id="questions">
            @component('questions.question', [
                'title' => 'Title',
                'label' => 'Question',
            ]);
            @endcomponent
        </div>
        <script>getCampId();</script>
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