var QuestionType = {
    TEXT: 1,
    PARAGRAPH: 2,
    CHOICES: 3,
    CHECKBOXES: 4,
    LIST: 5,
    FILE: 6,
};

function randId() {
    return Math.random().toString(36).substr(2, 5);
}

function addQuestion() {
    var id = randId();
    var block = jQuery("#question-block-1").first().clone().attr("id", `question-block-${id}`);
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

function deleteChoice(choice) {
    var choice_block = jQuery(choice).closest("[id^=question-block]").find(".form-check");
    if (choice_block.length <= 2)
        return;
    jQuery(choice).closest(".form-check").remove();
    return false;
}

function generateContent(name, label, parent, i, type) {
    var obj = null;
    switch (type) {
        case QuestionType.CHOICES:
           obj = jQuery.parseHTML(`
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="${name}[${parent}][${i}][]" id="${name}_${i}" value="${i}"/>
                    <div class="input-group mb-2">
                        <input type="text" required class="form-control" id="${name}_label_${i}" name="${name}_label[${parent}][${i}][]" placeholder="${label ? label : "Enter choice"}">
                        <div class="input-group-append">
                            <a href="#" class="btn btn-danger" onclick="return deleteChoice(this);">Delete</a>
                        </div>
                    </div>
                </div>
            `);
            break;
        case QuestionType.CHECKBOXES:

            break;
    }
    return obj;
}

function addChoice(target, name, type, parent, i) {
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
    var i = 2;
    if (value == QuestionType.CHOICES) {
        var add_choice_button = jQuery(jQuery.parseHTML(`
            <button class="btn btn-success mb-3" type="button"><span>Add More Choice</span></button>
        `));
        var name = "radio";
        add_choice_button.on('click', function (e) {
            e.preventDefault();
            addChoice(add, name, value, parentId, randId());
        });
        add.append(add_choice_button);
        addChoice(add, name, value, parentId, randId());
        addChoice(add, name, value, parentId, randId());
    }
    /*if (value == QuestionType.PARAGRAPH && block.find("textarea").length == 0) {
        var clazz = input.attr("class");
        var textbox = jQuery(document.createElement("textarea")).attr("class", clazz);
        input.replaceWith(textbox);
    }*/
}
