@extends('layouts.card')

@section('header')
    View Application Form
@endsection

@section('card_content')
    @foreach ($data as $pair)
        @php
            $question = $pair['question'];
            $key = $question->json_id;
            $answer = $pair['answer'];
        @endphp
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
                    @endphp
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
                            'noinline' => 1,
                            'required' => 1,
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
                            'required' => 1,
                        ])
                        @endcomponent
                    @elseif ($type == \App\Enums\QuestionType::FILE)
                        @if (isset($answer) && !empty($answer))
                            <a href="{{ route('camp_application.file_download', $key) }}">{{ $answer }}</a>
                        @else
                            <p>@lang('question.NoFileUploaded')</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    <script>
        jQuery(':radio').attr('disabled', true);
        jQuery(':checkbox').attr('disabled', true);
    </script>
    <div class="form-group row mb-0">
        <div class="col-12">
            @can('answer-edit')
                <a href="{{ route('camp_application.landing', $camp->id) }}" class="btn btn-secondary">@lang('app.Edit')</a>
                <a href="{{ route('camp_application.submit_application_form', $camp->id) }}" class="btn btn-success">@lang('app.Submit')</a>
            @endcan
        </div>
    </div>
@endsection