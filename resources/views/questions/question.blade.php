<div class="card mb-4" id="question-block-1">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <h2 id="question-title">{{ $title }}</h2>
            </div>
            <div class="col-sm-12 col-md-9">
                <div class="form-group row">
                    <label for="question-type" class="col-sm-2 col-md-2 col-form-label text-sm-left text-md-right">Type</label>
                    <div class="col-sm-10 col-md-10">    
                        <div class="form-row row">
                            <div class="col-6 col-sm-6 col-md-8">
                                @component('components.select', [
                                    'id' => 'question-type',
                                    'name' => 'type[]',
                                    'isform' => 0,
                                    'objects' => $question_types,
                                    'attributes' => 'onchange=selectionChanged(this);',
                                ])
                                @endcomponent
                            </div>
                            <div class="col-3 col-sm-3 col-md-4">
                                <a href="#" class="btn btn-danger float-right" onclick="return deleteQuestion(this);">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <legend></legend>
        <div class="form-group row">
            <label for="question" class="col-sm-12 col-md-2 col-form-label">{{ $label }}</label>
            <div class="col-sm-12 col-md-10">
                <input type="text" class="form-control" id="question" name="question[]" placeholder="{{ trans('question.EnterQuestionPlaceholder') }}">
            </div>
        </div>
    </div>
</div>