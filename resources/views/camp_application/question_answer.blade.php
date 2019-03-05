@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang ('app.Camp Application Form')
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
                            @component('components.file_upload', [
                                'name' => $key,
                                'value' => $value,
                                'args' => $value ? $json['answer_id'][$key] : null,
                                'upload' => 1,
                                'download_route' => $value ? 'camp_application.answer_file_download' : null,
                                'delete_route' => $value ? 'camp_application.answer_file_delete' : null,
                            ])
                            @endcomponent
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Save'),
            ])
            @endcomponent
            <a href="{{ route('camp_application.answer_view', $question_set->id) }}" class="btn btn-success">@lang('app.Next')</a>
        </div>
    </form>
@endsection