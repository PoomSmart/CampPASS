@extends('layouts.card')

@section('header')
    Camp Application Form
@endsection

@section('card_content')
    @if (!$eligible)
        You are not eligible for this camp.
    @elseif ($quota_exceed)
        The quota is full.
    @elseif ($already_applied)
        You already applied for this camp.
    @elseif (!empty($json))
        <form method="POST" action="{{ route('camp_application.store') }}">
            @csrf
            <input name="camp_id" id="camp_id" type="hidden" value="{{ $camp->id }}">
            @foreach ($json['question'] as $key => $text)
                <div class="row">
                    <div class="col-12">
                        <h3 id="question-title">{{ $text }}</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-4">
                            <?php
                                $type = (int)$json['type'][$key];
                                $required = isset($json['question_required'][$key]);
                            ?>
                            <!-- ? TODO: Simplify radio and checkbox -->
                            @if ($type == \App\Enums\QuestionType::TEXT)
                                <input type="text" class="form-control" name="{{ $key }}" value="{{ isset($answers[$key]) ? $answers[$key] : "" }}"
                                    @if ($required)
                                        required
                                    @endif
                                >
                            @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                                <textarea class="form-control" name="{{ $key }}"
                                    @if ($required)
                                        required
                                    @endif
                                >{{ isset($answers[$key]) ? $answers[$key] : "" }}</textarea>
                            @elseif ($type == \App\Enums\QuestionType::CHOICES)
                                <?php $set_required = false; ?>
                                @foreach ($json['radio_label'][$key] as $id => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="{{ $key }}" id="{{ $id }}" value="{{ $id }}"
                                            @if (!$set_required)
                                                <?php $set_required = true; ?>
                                                required
                                            @endif
                                            @if (isset($answers[$key]) && $id == $answers[$key])
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            @elseif ($type == \App\Enums\QuestionType::CHECKBOXES)
                                @foreach ($json['checkbox_label'][$key] as $id => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="{{ $key }}[]" id="{{ $id }}" value="{{ $id }}"
                                            @if (isset($answers[$key]) && in_array($id, $answers[$key], true))
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            @elseif ($type == \App\Enums\QuestionType::FILE)
                                <!-- TODO: Complete file type answer -->
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @component('components.submit', ['label' => trans('app.Save')])
            @slot('postcontent')
                <a href="{{ route('camp_application.question_review', $question_set->id) }}" class="btn btn-success">Next</a>
            @endslot
            @endcomponent
        </form>
    @endif
@endsection