@extends('layouts.card')

@section('header')
    @lang('app.CampApplicationForm')
@endsection

@section('card_content')
    @foreach ($data as $pair)
        @php
            $question = $pair['question'];
            $type = $question->type;
            $key = $question->json_id;
            $answer = $pair['answer'];
            $required = isset($json['question_required'][$key]);
        @endphp
        <div class="row">
            <div class="col-12">
                <h5><label
                    id="question-title"
                    @if ($required)
                        required
                    @endif
                    >{{ $json['question'][$key] }}</label></h5>
            </div>
            <div class="col-12">
                <div class="mb-4">
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
                            'idx' => 1,
                            'simple_id' => 1,
                            'required' => 1,
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
                            'required' => 1,
                            'radio_class' => 'w-100',
                        ])
                        @endcomponent
                    @elseif ($type == \App\Enums\QuestionType::FILE)
                        @if (isset($answer) && !empty($answer))
                            <a href="{{ route('camp_application.answer_file_download', $key) }}">{{ $answer }}</a>
                        @else
                            <p>@lang('question.NoFileUploaded')</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    <script>
        jQuery(":radio,:checkbox").attr("disabled", true);
    </script>
    <div class="form-group row mb-0">
        <div class="col-12 text-center">
            @can('answer-edit')
                <a href="{{ route('camp_application.landing', $camp->id) }}" class="btn btn-info"><i class="fas fa-pencil-alt mr-2 fa-xs"></i>@lang('app.Edit')</a>
                <a href="{{ route('camp_application.submit_application_form', $camp->id) }}" class="btn btn-success"><i class="far fa-save mr-2 fa-xs"></i>@lang('app.Submit')</a>
            @endcan
        </div>
    </div>
@endsection