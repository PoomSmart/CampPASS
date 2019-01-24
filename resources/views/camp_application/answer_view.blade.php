@extends('layouts.card')

@section('header')
    View Application Form
@endsection

@section('card_content')
    @foreach ($data as $pair)
        <?php
            $question = $pair['question'];
            $key = $question->json_id;
            $answer = $pair['answer'];
        ?>
        <div class="row">
            <div class="col-12">
                <h3 id="question-title">{{ $json['question'][$key] }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <?php
                        $type = $question->type;
                        $key = $question->json_id;
                        $required = isset($json['question_required'][$key]);
                    ?>
                    <!-- ? TODO: Simplify radio and checkbox -->
                    @if ($type == \App\Enums\QuestionType::TEXT)
                        <input type="text" readonly class="form-control" name="{{ $key }}" value="{{ $answer }}"
                            @if ($required)
                                required
                            @endif
                        >
                    @elseif ($type == \App\Enums\QuestionType::PARAGRAPH)
                        <textarea class="form-control" readonly name="{{ $key }}"
                            @if ($required)
                                required
                            @endif
                        >{{ $answer }}</textarea>
                    @elseif ($type == \App\Enums\QuestionType::CHOICES)
                        @foreach ($json['radio_label'][$key] as $id => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="{{ $key }}" id="{{ $id }}" value="{{ $id }}"
                                    @if ($id == $answer)
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
                                    @if (!is_null($answer) && in_array($id, $answer, true))
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
    <script>
        jQuery(':radio:not(:checked)').attr('disabled', true);
        jQuery(':checkbox:not(:checked)').attr('disabled', true);
    </script>
    <div class="form-group row mb-0">
        <div class="col-12">
            @can('answer-edit')
                <a href="{{ route('camp_application.landing', $camp->id) }}" class="btn btn-secondary">{{ trans('app.Edit') }}</a>
                <a href="{{ route('camp_application.submit_application_form', $camp->id) }}" class="btn btn-success">{{ trans('Submit') }}</a>
            @endcan
        </div>
    </div>
@endsection