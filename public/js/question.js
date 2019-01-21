// TODO: Add a warning for when the user tries to quit without saving

var QuestionType = {
    TEXT: 1,
    PARAGRAPH: 2,
    CHOICES: 3,
    CHECKBOXES: 4,
    FILE: 5,
};

var question_block_selector = "[id^=question-block]";
var add_choice_HTML = `
    <button class="btn btn-success mb-3" type="button"><span>Add More Choice</span></button>
`;
var add_checkbox_HTML = `
    <button class="btn btn-success mb-3" type="button"><span>Add More Checkbox</span></button>
`;

var campId = -1;

function getCampId(id) {
    campId = id;
}

function randId() {
    return `${campId}-${Math.random().toString(36).substr(2, 5)}`;
}

function readJSON(json) {
    if (!json) {
        console.log("Info: JSON is null");
        return
    };
    if (json.camp_id != campId) {
        console.log("Error: Camp ID mismatched");
        return;
    }
    var old_block = jQuery(question_block_selector).first();
    Object.keys(json.question).forEach(function(key) {
        var block = old_block.clone();
        var id = key;
        var question_text = json.question[id];
        var question_type = parseInt(json.type[id]);
        block.attr("id", `question-block-${id}`);
        block.find("#question-type").attr("name", `type[${id}]`).val(question_type);
        block.find("#question").attr("name", `question[${id}]`).val(question_text);
        var add = block.find("#additional-content");
        switch (question_type) {
            case QuestionType.CHOICES:
                if (id in json.radio_label) {
                    var choice_texts = json.radio_label[id];
                    var selection = json.radio[id];
                    addAdditionalContent(block, add, question_type, id, choice_texts, selection);
                }
                break;
            case QuestionType.CHECKBOXES:
                if (id in json.checkbox_label) {
                    var checkbox_texts = json.checkbox_label[id];
                    addAdditionalContent(block, add, question_type, id, checkbox_texts, null);
                }
                break;
        }
        jQuery("#questions").append(block);
    });
    old_block.remove();
}

function addQuestion() {
    var id = randId();
    var block = jQuery(question_block_selector).first().clone().attr("id", `question-block-${id}`);
    block.find("#question-type").attr("name", `type[${id}]`);
    block.find("#question").attr("name", `question[${id}]`).val("");
    block.find("#additional-content").empty();
    jQuery("#questions").append(block);
}

function deleteQuestion(button) {
    if (jQuery(question_block_selector).length <= 1)
        return;
    jQuery(button).closest(question_block_selector).remove();
    return false;
}

function deleteChoiceOrCheckbox(item, minimum) {
    var block = jQuery(item).closest(question_block_selector).find(".entry");
    if (block.length <= minimum)
        return;
    jQuery(item).closest(".entry").remove();
    return false;
}

function generateContent(name, value, parentId, i, type) {
    var obj = null;
    switch (type) {
        case QuestionType.CHOICES:
            obj = jQuery.parseHTML(`
                <div class="entry">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="${name}[${parentId}]" id="${name}_${i}" value="${i}"/>
                            </div>
                        </div>
                        <input type="text" required autocomplete="disabled" class="form-control" id="${name}_label_${i}" name="${name}_label[${parentId}][${i}]" placeholder="Enter choice" value="${value ? value : ""}"}">
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
                        <input type="text" required autocomplete="disabled" class="form-control" id="${name}_label_${i}" name="${name}_label[${parentId}][${i}]" placeholder="Enter checkbox label" value="${value ? value : ""}"}">
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

function addRadioOrCheckbox(target, name, type, parentId, i) {
    target.append(generateContent(name, null, parentId, i, type));
}

function addAdditionalContent(block, add, type, parentId, entries, value) {
    if (type == QuestionType.CHOICES) {
        var add_choice_button = jQuery(jQuery.parseHTML(add_choice_HTML));
        add_choice_button.on('click', function (e) {
            e.preventDefault();
            addRadioOrCheckbox(add, "radio", type, parentId, randId());
        });
        add.append(add_choice_button);
        if (entries) {
            Object.keys(entries).forEach(function(choice_id) {
                var choice_text = entries[choice_id];
                add.append(generateContent("radio", choice_text, parentId, choice_id, type));
            });
            if (value)
                block.find(`#radio_${value}`).attr("checked", true);
        } else {
            addRadioOrCheckbox(add, "radio", type, parentId, randId());
            add.find("input[type=radio]").first().attr("checked", true);
            addRadioOrCheckbox(add, "radio", type, parentId, randId());
        }
    } else if (type == QuestionType.CHECKBOXES) {
        var add_checkbox_button = jQuery(jQuery.parseHTML(add_checkbox_HTML));
        add_checkbox_button.on('click', function (e) {
            e.preventDefault();
            addRadioOrCheckbox(add, "checkbox", type, parentId, randId());
        });
        add.append(add_checkbox_button);
        if (entries) {
            Object.keys(entries).forEach(function(checkbox_id) {
                var checkbox_text = entries[checkbox_id];
                add.append(generateContent("checkbox", checkbox_text, parentId, checkbox_id, type));
            });
        } else
            addRadioOrCheckbox(add, "checkbox", type, parentId, randId());
    }
}

function selectionChanged(select) {
    var block = jQuery(select).closest(question_block_selector);
    var parentId = block.attr("id").substr(15);
    // remove the old content of the question block
    var add = block.find("#additional-content");
    add.empty();
    var type = parseInt(select.value);
    addAdditionalContent(block, add, type, parentId, null, null);
}
