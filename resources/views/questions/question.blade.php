<div class="card mb-4" id="question-block-{{ $camp_id }}-00000">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-3">
                <h2 id="question-title">{{ $title }}</h2>
            </div>
            <div class="col-12 col-sm-12 col-md-9">
                <div class="form-group row">
                    <label for="question-type" class="col-12 col-sm-2 col-md-2 col-form-label text-sm-left text-md-right">{{ trans('question.Type') }}</label>
                    <div class="col-sm-10 col-md-10">    
                        <div class="input-group">
                            @component('components.select', [
                                'id' => 'question-type',
                                'name' => "type[{$camp_id}-00000]",
                                'isform' => 0,
                                'objects' => $question_types,
                                'attributes' => 'required onchange=selectionChanged(this);',
                            ])
                            @endcomponent
                            <div class="input-group-append">
                                <a href="#" class="btn btn-danger float-right" onclick="return deleteQuestion(this);">{{ trans('app.Delete') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <label for="question" class="col-sm-12 col-md-3 col-form-label">{{ $label }}</label>
            <div class="col-sm-12 col-md-9">
                <div class="input-group">
                    <input type="text" required autocomplete="disabled" class="form-control" id="question" name="question[{{ $camp_id }}-00000]" placeholder="{{ trans('question.EnterQuestionPlaceholder') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <input type="checkbox" id="question-required" name="question_required[{{ $camp_id }}-00000]" aria-label="Check to require answer for this question">
                            <span class="ml-1">{{ trans('app.Required') }}</span>
                        </div>
                        <div class="input-group-text">
                            <input type="checkbox" id="question-graded" name="question_graded[{{ $camp_id }}-00000]" aria-label="Check to require this question to be graded">
                            <span class="ml-1">{{ trans('question.Graded') }}</span>
                        </div>
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