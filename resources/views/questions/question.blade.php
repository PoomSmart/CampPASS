<div class="card mb-4" id="question-block-00000">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-3">
                <h2 id="question-title">{{ $title }}</h2>
            </div>
            <div class="col-12 col-sm-12 col-md-9">
                <div class="form-group row">
                    <label for="question-type" class="col-12 col-sm-2 col-md-2 col-form-label text-sm-left text-md-right">Type</label>
                    <div class="col-sm-10 col-md-10">    
                        <div class="input-group">
                            @component('components.select', [
                                'id' => 'question-type',
                                'name' => 'type[00000]',
                                'isform' => 0,
                                'objects' => $question_types,
                                'attributes' => 'required onchange=selectionChanged(this);',
                            ])
                            @endcomponent
                            <div class="input-group-append">
                                <a href="#" class="btn btn-danger float-right" onclick="return deleteQuestion(this);">Delete</a>
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
                <input type="text" class="form-control" id="question" name="question[00000]" placeholder="{{ trans('question.EnterQuestionPlaceholder') }}">
            </div>
        </div>
        <div id="additional-content"></div>
    </div>
</div>