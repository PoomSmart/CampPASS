var QuestionType = {
    TEXT: 1,
    PARAGRAPH: 2,
    CHOICES: 3,
    CHECKBOXES: 4,
    FILE: 5,
};

function randId() {
    return Math.random().toString(36).substr(2, 5);
}

function addQuestion() {
    var id = randId();
    var block = jQuery("[id^=question-block]").first().clone().attr("id", `question-block-${id}`);
    block.find("#question-type").attr("name", `type[${id}]`);
    block.find("#question").attr("name", `question[${id}]`);
    block.find("#additional-content").empty();
    jQuery("#questions").append(block);
}

function deleteQuestion(button) {
    if (jQuery("[id^=question-block]").length <= 1)
        return;
    jQuery(button).closest("[id^=question-block]").remove();
    return false;
}

function deleteChoiceOrCheckbox(item, minimum) {
    var block = jQuery(item).closest("[id^=question-block]").find(".entry");
    if (block.length <= minimum)
        return;
    jQuery(item).closest(".entry").remove();
    return false;
}

function generateContent(name, label, parent, i, type) {
    var obj = null;
    switch (type) {
        case QuestionType.CHOICES:
           obj = jQuery.parseHTML(`
                <div class="entry">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" required name="${name}[${parent}][${i}][]" id="${name}_${i}" value="${i}"/>
                            </div>
                        </div>
                        <input type="text" required class="form-control" id="${name}_label_${i}" name="${name}_label[${parent}][${i}][]" placeholder="${label ? label : "Enter choice"}">
                        <div class="input-group-append">
                            <a href="#" class="btn btn-danger" onclick="return deleteChoiceOrCheckbox(this, 2);">Delete</a>
                        </div>
                    </div>
                </div>
            `);
            break;
        case QuestionType.CHECKBOXES:
            obj = jQuery.parseHTML(`
                <div class="entry">
                    <div class="input-group mb-2">
                        <input type="text" required class="form-control" id="${name}_label_${i}" name="${name}_label[${parent}][${i}][]" placeholder="${label ? label : "Enter checkbox label"}">
                        <div class="input-group-append">
                            <a href="#" class="btn btn-danger" onclick="return deleteChoiceOrCheckbox(this, 1);">Delete</a>
                        </div>
                    </div>
                </div>
            `);
            break;
    }
    return obj;
}

function addRadioOrCheckbox(target, name, type, parent, i) {
    target.append(generateContent(name, null, parent, i, type));
}

function selectionChanged(select) {
    var value = parseInt(select.value);
    var block = jQuery(select).closest("[id^=question-block]");
    var parentId = block.attr("id").substr(15);
    var input = block.find("#question");
    // remove the old additional content of the question block
    var add = block.find("#additional-content");
    add.empty();
    if (value == QuestionType.CHOICES) {
        var add_choice_button = jQuery(jQuery.parseHTML(`
            <button class="btn btn-success mb-3" type="button"><span>Add More Choice</span></button>
        `));
        add_choice_button.on('click', function (e) {
            e.preventDefault();
            addRadioOrCheckbox(add, "radio", value, parentId, randId());
        });
        add.append(add_choice_button);
        addRadioOrCheckbox(add, "radio", value, parentId, randId());
        addRadioOrCheckbox(add, "radio", value, parentId, randId());
    } else if (value == QuestionType.CHECKBOXES) {
        var add_checkbox_button = jQuery(jQuery.parseHTML(`
            <button class="btn btn-success mb-3" type="button"><span>Add More Checkbox</span></button>
        `));
        add_checkbox_button.on('click', function (e) {
            e.preventDefault();
            addRadioOrCheckbox(add, "checkbox", value, parentId, randId());
        });
        add.append(add_checkbox_button);
        addRadioOrCheckbox(add, "checkbox", value, parentId, randId());
    }
}
