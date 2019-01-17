i = 0;

function questionHTML(title, label, i) { return `
    <div>
        <div class="row">
            <div class="col-sm-12">
                <h2>${title}</h2>
            </div>
        </div>
        <legend></legend>
        <div class="form-group row">
            <label for="question_${i}" class="col-sm-12 col-md-2 col-form-label">${label}</label>
            <div class="col-sm-12 col-md-10">
                <input type="text" class="form-control" id="question_${i}" name="question[]" placeholder="Enter Question..">
            </div>
        </div>
    </div>
`};

function addQuestion() {
    jQuery("#questions").append(jQuery.parseHTML(questionHTML('Title', 'Question', i++)));
}