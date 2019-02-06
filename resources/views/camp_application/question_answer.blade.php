@extends('layouts.card')

@section('header')
    Camp Application Form
@endsection

<!-- TODO: Decide what to do when the camper makes changes and presses next without saving first -->
@section('card_content')
    @if (isset($already_applied))
        You already applied for this camp.
    @elseif (empty($json))
        No questions in here.
    @else
        <form method="POST" action="{{ route('camp_application.store') }}" enctype="multipart/form-data">
            @csrf
            <input name="camp_id" id="camp_id" type="hidden" value="{{ $camp->id }}">
            @foreach ($json['question'] as $key => $text)
                <?php
                    $type = (int)$json['type'][$key];
                    $required = isset($json['question_required'][$key]);
                    $value = isset($json['answer'][$key]) ? $json['answer'][$key] : null;
                ?>
                <div class="row">
                    <div class="col-12">
                        <h3 id="question-title">{{ $text }}</h2>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            @if ($type == \App\Enums\QuestionType::TEXT)
                                @component('components.input', [
                                    'name' => $key,
                                    'value' => $value,
                                    'attributes' => $required ? 'required' : '',
                                ])
                                @endcomponent
                            @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                                @component('components.input', [
                                    'name' => $key,
                                    'value' => $value,
                                    'textarea' => 1,
                                    'attributes' => $required ? 'required' : '',
                                ])
                                @endcomponent
                            @elseif ($type == \App\Enums\QuestionType::CHOICES)
                                @component('components.radio', [
                                    'name' => $key,
                                    'value' => $value,
                                    'objects' => $json['radio_label'][$key],
                                    'idx' => 1,
                                    'noinline' => 1,
                                    'required' => $required ? 'required' : '',
                                ])
                                @endcomponent
                            @elseif ($type == \App\Enums\QuestionType::CHECKBOXES)
                                @component('components.radio', [
                                    'name' => $key,
                                    'value' => $value,
                                    'type' => 'checkbox',
                                    'objects' => $json['checkbox_label'][$key],
                                    'idx' => 1,
                                    'noinline' => 1,
                                    'required' => $required ? 'required' : '',
                                ])
                                @endcomponent
                            @elseif ($type == \App\Enums\QuestionType::FILE)
                                @if ($value)
                                    <a href="{{ route('camp_application.answer_file_download', $json['answer_id'][$key]) }}">{{ $value }}</a>
                                    <a class="btn btn-danger" href="{{ route('camp_application.answer_file_delete', $json['answer_id'][$key]) }}">@lang('app.Delete')</a>
                                @endif
                                <input type="file" class="form-control-file" name="{{ $key }}">
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @component('components.submit', ['label' => trans('app.Save')])
            @slot('postcontent')
                <a href="{{ route('camp_application.answer_view', $question_set->id) }}" class="btn btn-success">@lang('app.Next')</a>
            @endslot
            @endcomponent
        </form>
    @endif
@endsection