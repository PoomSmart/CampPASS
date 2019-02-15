@extends('layouts.card')

@section('header')
    Grade Application Form of <a href="{{ route('profiles.show', $camper) }}" target="_blank">{{ $camper->getFullName() }}</a>
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
            <div class="row mb-4">
                <div class="col-md-8">
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
                                        'noinline' => 1,
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
                                        'noinline' => 1,
                                    ])
                                    @endcomponent
                                @elseif ($type == \App\Enums\QuestionType::FILE)
                                    @if (isset($answer))
                                        <a href="{{ route('camp_application.file_download', $key) }}">{{ $answer }}</a>
                                    @else
                                        <p>@lang('question.NoFileUploaded')</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 my-auto">
                    @if (isset($json['question_graded'][$key]))
                        @php
                            $full_score = $json['question_full_score'][$key];
                            $score = isset($json['question_scored'][$key]) ? $json['question_scored'][$key] : null;
                        @endphp
                        @component('components.numeric_range', [
                            'name' => 'manual_score',
                            'range_id' => $key,
                            'min' => 0.0,
                            'max' => $full_score,
                            'step' => 0.1,
                            'value' => $graded ? $score : null,
                            'readonly' => isset($json['question_lock'][$key]),
                            'object' => isset($object) ? $object : null,
                        ])
                        @endcomponent
                        <p class="text-center text-muted mt-1">{{ "Out of {$full_score}" }}</p>
                    @endif
                </div>
            </div>
        @endforeach
        @component('components.submit', [
            'label' => trans('app.Save'),
            'disabled' => $form_score->finalized,
        ])
        @slot('postcontent')
            <a class="btn btn-danger{{ $form_score->finalized ? ' disabled' : '' }}" href="{{ route('qualification.form_finalize', $form_score) }}">@lang('qualification.Finalize')</a>
            <a class="btn btn-secondary" href="{{ route('camps.show', $camp) }}">@lang('app.Back')</a>
        @endslot
        @endcomponent
    </form>
    <script>
        jQuery(':radio').attr('disabled', true);
        jQuery(':checkbox').attr('disabled', true);
    </script>
@endsection