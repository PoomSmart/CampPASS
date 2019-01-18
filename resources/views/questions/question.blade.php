<div id="question-block-1">
    <div class="row">
        <div class="col-sm-12 col-md-3">
            <h2 id="question-title-1">{{ $title }}</h2>
        </div>
        <div class="col-sm-12 col-md-9">
            <div class="form-group row">
                <label for="question-type-1" class="col-sm-2 col-md-2 col-form-label text-sm-left text-md-right">Type</label>
                <div class="col-sm-10 col-md-10">    
                    <div class="form-row row">
                        <div class="col-6 col-sm-6 col-md-8">
                            @component('components.select', [
                                'id' => 'question-type-1',
                                'name' => 'type[]',
                                'objects' => [],
                            ])
                            @endcomponent
                        </div>
                        <div class="col-3 col-sm-3 col-md-4">
                            <button type="submit" class="btn btn-danger float-right">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <legend></legend>
    <div class="form-group row">
        <label for="question_1" class="col-sm-12 col-md-2 col-form-label">{{ $label }}</label>
        <div class="col-sm-12 col-md-10">
            <input type="text" class="form-control" id="question_1" name="question[]" placeholder="Enter Question..">
        </div>
    </div>
</div>