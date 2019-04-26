@php
    $finalized = isset($object) && $object->finalized;
@endphp
<div class="card mb-4" id="question-block-{{ $camp->id }}-00000">
    <div class="card-body">
        <div class="form-group row">
            <div class="col-12">
                @component('components.input', [
                    'name' => "question[{$camp->id}-00000]",
                    'label' => $label,
                    'label_id' => 'question-label',
                    'id' => 'question',
                    'textarea' => 1,
                    'required' => 1,
                    'disabled' => $finalized,
                    'attributes' => 'autocomplete="disabled"',
                    'placeholder' => trans('question.EnterQuestionPlaceholder'),
                ])
                @endcomponent
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <label for="question-type" class="col-12 col-sm-2 col-md-2 col-form-label">@lang('question.Type')</label>
            <div class="col-12 col-sm-10 col-md-10">
                <div class="input-group">
                    @component('components.select', [
                        'id' => 'question-type',
                        'name' => "type[{$camp->id}-00000]",
                        'isform' => 0,
                        'objects' => $question_types,
                        'required' => 1,
                        'attributes' => 'onchange=selectionChanged(this);',
                        'disabled' => $finalized,
                    ])
                    @endcomponent
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <input type="checkbox" id="question-required" name="question_required[{{ $camp->id }}-00000]" aria-label="Check to require answer for this question">
                            <span class="ml-1">@lang('app.Required')</span>
                        </div>
                        <div class="input-group-text">
                            <input type="checkbox" id="question-graded" name="question_graded[{{ $camp->id }}-00000]" aria-label="Check to require this question to be graded">
                            <span class="ml-1">@lang('question.GradingRequired')</span>
                        </div>
                        @if (!$finalized)
                            <a href="#" id="question-delete" class="btn btn-danger" onclick="return deleteQuestion(this);">@lang('app.Delete')</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Requirement: what has to be graded must be required
            var block = jQuery("[id^=question-block]").first();
            block.find("#question-graded").click(function(e) {
                block.find("#question-required").attr("onclick", this.checked ? "this.checked=!this.checked" : null).prop("checked", this.checked);
            });
        </script>
        <div id="additional-content"></div>
    </div>
</div>