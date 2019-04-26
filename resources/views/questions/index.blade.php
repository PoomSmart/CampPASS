@extends('layouts.blank')

@section('script')
    <script src="{{ asset('js/question.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='minimum_score']").inputSpinner();
        });
    </script>
    <script src="{{ asset('js/checkbox-require.js') }}"></script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang('question.CreateQuestions')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('content')
    @component('components.dialog', [
        'body' => trans('question.SureFinalizeQuestionSet'), 
        'confirm_type' => 'danger',
    ])
    @endcomponent
    <form id="form" method="POST" action="{{ route('questions.store', $camp->id) }}">
        @csrf
        <div class="col-12">
            @component('components.input', [
                'name' => 'minimum_score',
                'label' => trans('question.MinimumScore'),
                'type' => 'number',
                'placeholder' => trans('question.EnterMinimumScore'),
                'no_form_control_class' => 1,
                'attributes' => 'min=1 step=1'.($object ? ' max='.$object->total_score : ''),
                'object' => $object,
            ])
            @endcomponent
        </div>
        <div id="questions" class="mt-4">
            @component('questions.question', [
                'label' => trans('question.Question'),
                'i' => 1,
            ]);
            @endcomponent
        </div>
        <script>getInfo("{!! trans('question.AddMoreChoice') !!}", "{!! trans('question.AddMoreCheckbox') !!}", "{!! $camp->id !!}", {!! $object && $object->finalized !!});</script>
        @if (!empty($json))
            <script>
                var client_json = JSON.parse({!! $json !!});
                readJSON(client_json);
            </script>
        @endif
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Save'),
                'glyph' => 'far fa-save fa-xs',
            ])
            @endcomponent
            <button class="btn btn-danger" {{ $object && $object->finalized ? 'disabled' : null }} type="button" data-toggle="modal" data-target="#modal" data-action="{{ route('questions.finalize', $camp->id) }}"><i class="fas fa-check mr-2 fa-xs"></i>{{ $object && $object->finalized ? trans('question.Finalized') : trans('question.Finalize') }}</button>
            <button class="btn btn-success" {{ $object && $object->finalized ? 'disabled' : null }} type="button" onclick="addQuestion();"><i class="fas fa-plus mr-2 fa-xs"></i>@lang('question.AddMoreQuestion')</button>
        </div>
    </form>
    @if ($object && $object->finalized)
        <script>
            jQuery(":radio,:checkbox").attr("disabled", true);
        </script>
    @endif
@endsection