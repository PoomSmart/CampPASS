@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang('app.CampApplicationForm')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('card_content')
    @php $has_any_answers = false @endphp
    @component('components.dialog', [
        'confirm_type' => 'warning',
        'confirm_label' => trans('app.OK'),
        'title' => trans('registration.Attention'),
        'body' => trans('registration.PleaseCheckFormSave'),
        'id' => 'recheck-modal',
        'nosubmit' => 1,
    ]) 
    @endcomponent
    <div class="col-12 mb-4 text-center">
        <img class="img-fluid" style="max-height: 400px;" src="{{ $camp->getBannerPath($actual = false, $display = true) }}">
    </div>
    <form method="POST" id="form" action="{{ route('camp_application.store', $camp->id) }}" enctype="multipart/form-data">
        @csrf
        @foreach ($json['question'] as $key => $text)
            @php
                $type = (int)$json['type'][$key];
                $required = isset($json['question_required'][$key]);
                $value = isset($json['answer'][$key]) ? $json['answer'][$key] : null;
                if (!is_null($value))
                    $has_any_answers = true;
            @endphp
            <div class="row">
                <div class="col-12">
                    <h5><label
                        id="question-title"
                        @if ($required)
                            required
                        @endif
                    >{{ $text }}</label></h5>
                </div>
                <div class="col-12">
                    <div class="mb-4">
                        @if ($type == \App\Enums\QuestionType::TEXT)
                            @component('components.input', [
                                'name' => $key,
                                'value' => $value,
                                'required' => $required,
                            ])
                            @endcomponent
                        @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                            @component('components.input', [
                                'name' => $key,
                                'value' => $value,
                                'textarea' => 1,
                                'required' => $required,
                            ])
                            @endcomponent
                        @elseif ($type == \App\Enums\QuestionType::CHOICES)
                            @component('components.radio', [
                                'name' => $key,
                                'value' => $value,
                                'objects' => $json['radio_label'][$key],
                                'idx' => 1,
                                'required' => $required,
                                'radio_class' => 'w-100',
                            ])
                            @endcomponent
                        @elseif ($type == \App\Enums\QuestionType::CHECKBOXES)
                            @component('components.radio', [
                                'name' => $key,
                                'value' => $value,
                                'type' => 'checkbox',
                                'objects' => $json['checkbox_label'][$key],
                                'idx' => 1,
                                'required' => $required,
                                'radio_class' => 'w-100',
                            ])
                            @endcomponent
                        @elseif ($type == \App\Enums\QuestionType::FILE)
                            @component('components.file_upload', [
                                'name' => $key,
                                'value' => $value,
                                'args' => $value ? $json['answer_id'][$key] : null,
                                'upload' => 1,
                                'required' => $required,
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
                'glyph' => 'far fa-save fa-xs',
            ])
            @endcomponent
            @component('components.a', [
                'class' => 'btn btn-success',
                'id' => 'next-page',
                'href' => route('camp_application.answer_view', $question_set->id),
                'glyph' => 'fas fa-arrow-right fa-xs',
                'label' => trans('app.Next'),
                'disabled' => !$has_any_answers,
            ])
            @endcomponent
        </div>
        <script>
            jQuery.fn.isValid = function () {
                var validate = true;
                this.each(function () {
                    if (this.checkValidity () == false)
                        validate = false;
                });
                return validate;
            };
            jQuery("#next-page").click(function (e) {
                var form = jQuery("#form");
                if (!form.isValid()){
                    e.preventDefault();
                    $('#recheck-modal').modal()
                }
            });
        </script>
    </form>
@endsection