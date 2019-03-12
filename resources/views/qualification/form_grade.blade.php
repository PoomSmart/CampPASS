@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name^='manual_score_']").inputSpinner();
        });
    </script>
@endsection

@section('header')
    Application Form Grading
@endsection

@section('subheader')
    <a href="{{ route('profiles.show', $camper->id) }}" target="_blank">{{ $camper->getFullName() }}</a>
@endsection

@section('card_content')
    <div class="row">
        <div class="col-12 text-muted text-right">
            {{ $score_report }}
        </div>
    </div>
    <form action="{{ route('qualification.save_manual_grade', [ $form_score->registration_id, $form_score->question_set_id ]) }}" method="POST">
        @csrf
        @foreach ($data as $pair)
            @php
                $question = $pair['question'];
                $key = $question->json_id;
                $answer = $pair['answer'];
            @endphp
            <div class="row mb-3">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="question-title">{{ $json['question'][$key] }}</h4>
                        </div>
                        <div class="col-12">
                            <div class="mb-4">
                                @php
                                    $type = $question->type;
                                    $key = $question->json_id;
                                    $required = isset($json['question_required'][$key]);
                                    $graded = isset($json['question_graded'][$key]);
                                @endphp
                                @if ($type == \App\Enums\QuestionType::TEXT)
                                    @component('components.input', [
                                        'name' => $key,
                                        'value' => $answer,
                                        'simple_id' => 1,
                                        'attributes' => 'readonly disabled',
                                    ])
                                    @endcomponent
                                @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                                    @component('components.input', [
                                        'name' => $key,
                                        'value' => $answer,
                                        'textarea' => 1,
                                        'simple_id' => 1,
                                        'attributes' => 'readonly disabled',
                                    ])
                                    @endcomponent
                                @elseif ($type == \App\Enums\QuestionType::CHOICES)
                                    @component('components.radio', [
                                        'name' => $key,
                                        'value' => $answer,
                                        'objects' => $json['radio_label'][$key],
                                        'correct_answer' => $graded ? $json['radio'][$key] : null,
                                        'idx' => 1,
                                        'simple_id' => 1,
                                        'radio_class' => 'w-100',
                                    ])
                                    @endcomponent
                                @elseif ($type == \App\Enums\QuestionType::CHECKBOXES)
                                    @component('components.radio', [
                                        'name' => $key,
                                        'value' => $answer,
                                        'type' => 'checkbox',
                                        'objects' => $json['checkbox_label'][$key],
                                        'idx' => 1,
                                        'simple_id' => 1,
                                        'radio_class' => 'w-100',
                                    ])
                                    @endcomponent
                                @elseif ($type == \App\Enums\QuestionType::FILE)
                                    @if (isset($answer))
                                        <a href="{{ route('camp_application.answer_file_download', $key) }}">{{ $answer }}</a>
                                    @else
                                        <p>@lang('question.NoFileUploaded')</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 my-auto">
                    @if (isset($json['question_graded'][$key]))
                        @php
                            $full_score = $json['question_full_score'][$key];
                            $score = isset($json['question_scored'][$key]) ? $json['question_scored'][$key] : null;
                            $readonly = isset($json['question_lock'][$key]) && $json['question_lock'][$key];
                        @endphp
                        @component('components.input', [
                            'name' => "manual_score_{$key}",
                            'type' => 'number',
                            'no_form_control_class' => 1,
                            'attributes' => "min=0.0 max={$full_score} step=0.5 data-decimals=1 data-suffix=/{$full_score}".($readonly ? " buttonsClass='disabled'" : null),
                            'value' => $graded ? $score : null,
                            'readonly' => $readonly,
                            'object' => isset($object) ? $object : null,
                        ])
                        @endcomponent
                    @endif
                </div>
            </div>
            <hr>
        @endforeach
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Save'),
                'disabled' => $form_score->finalized,
            ])
            @endcomponent
            <a class="btn btn-danger{{ $form_score->finalized ? ' disabled' : '' }}" href="{{ route('qualification.form_finalize', $form_score) }}">{{ $form_score->finalized ? trans('qualification.Finalized') : trans('qualification.Finalize') }}</a>
        </div>
    </form>
    <script>
        jQuery(":radio,:checkbox").attr("disabled", true);
    </script>
@endsection