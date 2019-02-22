@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    Camp Application Form
@endsection

@section('card_content')
    <form method="POST" id="form" action="{{ route('camp_application.store') }}" enctype="multipart/form-data">
        @csrf
        <input name="camp_id" id="camp_id" type="hidden" value="{{ $camp->id }}">
        @foreach ($json['question'] as $key => $text)
            @php
                $type = (int)$json['type'][$key];
                $required = isset($json['question_required'][$key]);
                $value = isset($json['answer'][$key]) ? $json['answer'][$key] : null;
            @endphp
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
        <div class="text-center">
            @component('components.submit', ['label' => trans('app.Save')])
            @endcomponent
            <a href="{{ route('camp_application.answer_view', $question_set->id) }}" class="btn btn-success">@lang('app.Next')</a>
        </div>
    </form>
@endsection