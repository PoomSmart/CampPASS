@extends('layouts.card')

@section('header')
    <!-- TODO: Link to camper profile page -->
    View Application Form of <a href="" target="_blank">{{ $camper->getFullName() }}</a>
@endsection

@section('card_content')
    <div class="row">
        <div class="col-12 text-muted text-right">
            {{ $score_report }}
        </div>
    </div>
    @foreach ($data as $pair)
        <?php
            $question = $pair['question'];
            $key = $question->json_id;
            $answer = $pair['answer'];
        ?>
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-12">
                        <h4 id="question-title">{{ $json['question'][$key] }}</h4>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <?php
                                $type = $question->type;
                                $key = $question->json_id;
                                $required = isset($json['question_required'][$key]);
                                $graded = isset($json['question_graded'][$key]);
                            ?>
                            @if ($type == \App\Enums\QuestionType::TEXT)
                                @component('components.input', [
                                    'name' => $key,
                                    'value' => $answer,
                                    'simple_id' => 1,
                                    'attributes' => 'readonly',
                                ])
                                @endcomponent
                            @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                                @component('components.input', [
                                    'name' => $key,
                                    'value' => $answer,
                                    'textarea' => 1,
                                    'simple_id' => 1,
                                    'attributes' => 'readonly',
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
                                    <p>{{ trans('question.NoFileUploaded') }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 my-auto">
                @if (isset($json['question_graded'][$key]))
                    <?php $full_score = $json['question_full_score'][$key] ?>
                    @component('components.numeric_range', [
                        'name' => 'manual_score', // TODO: save these values
                        'min' => 0,
                        'max' => $full_score,
                        'step' => 1,
                        'object' => isset($object) ? $object : null,
                    ])
                    @endcomponent
                    <p class="text-center text-muted mt-1">{{ "Out of {$full_score}" }}</p>
                @endif
            </div>
        </div>
    @endforeach
    <script>
        jQuery(':radio').attr('disabled', true);
        jQuery(':checkbox').attr('disabled', true);
    </script>
@endsection