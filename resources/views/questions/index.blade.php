@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/question.js') }}"></script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@section('header')
    @lang('question.CreateQuestions')
@endsection

@section('card_content')
    @component('components.dialog', [
        'body' => 'Are you sure you want to finalize this question set? You will not be able to edit the questions if it is finalized.',
        'confirm_type' => 'danger',
    ])
    @endcomponent
    <form id="form" method="POST" action="{{ route('questions.store', $camp_id) }}">
        @csrf
        @component('components.numeric_range', [
            'name' => 'score_threshold',
            'label' => trans('question.ScoreThreshold'),
            'placeholder' => trans('question.EnterThreshold'),
            'min' => 0.01,
            'max' => 1.0,
            'step' => 0.01,
            'object' => isset($object) ? $object : null,
        ])
        @endcomponent
        <div id="questions" class="mt-4">
            @component('questions.question', [
                'title' => trans('question.Title'),
                'label' => trans('question.Question'),
            ]);
            @endcomponent
        </div>
        <script>getInfo("{!! trans('question.AddMoreChoice') !!}", "{!! trans('question.AddMoreCheckbox') !!}", "{!! $camp_id !!}", {!! isset($object) && $object->finalized !!});</script>
        @if (!empty($json))
            <script>
                var client_json = JSON.parse({!! $json !!});
                readJSON(client_json);
            </script>
        @endif
        <div class="text-center">
            @component('components.submit', ['label' => trans('app.Save')])
            @endcomponent
            <button class="btn btn-danger{{ isset($object) && $object->finalized ? ' disabled' : '' }}" type="button" data-toggle="modal" data-target="#modal" data-action="{{ route('questions.finalize', $camp_id) }}">{{ isset($object) && $object->finalized ? trans('question.Finalized') : trans('question.Finalize') }}</button>
            <button class="btn btn-success{{ isset($object) && $object->finalized ? ' disabled' : '' }}" type="button" onclick="addQuestion();">@lang('question.AddMoreQuestion')</button>
            <a class="btn btn-secondary" href="{{ route('camps.index') }}">@lang('app.Back')</a>
        </div>
    </form>
    @if (isset($object) && $object->finalized)
        <script>
            jQuery(':radio').attr('disabled', true);
            jQuery(':checkbox').attr('disabled', true);
        </script>
    @endif
@endsection