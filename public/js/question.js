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
    var block = jQuery("#question-block-1").first().clone().attr("id", `question-block-${randId()}`);
    block.find("#question-title").val("Title").attr("id", `question-title`);
    block.find("label").val("Question").attr("for", `question`);
    block.find("input").attr("id", `question`);
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

function generateContent(name, label, i, type) {
    var obj = null;
    switch (type) {
        case QuestionType.CHOICES:
           obj = jQuery.parseHTML(`
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="${name}[]" id="${name}_${i}" value="${i}"/>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="${name}_label_${i}" name="${name}_label[]" placeholder="${label}">
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

function selectionChanged(select) {
    var value = parseInt(select.value);
    var block = jQuery(select).closest("[id^=question-block]");
    var input = block.find("#question");
    // remove the old additional content of the question block
    var add = block.find("#additional-content");
    add.empty();
    var i = 1;
    if (value == QuestionType.CHOICES) {
        var name = "radio";
        add.append(generateContent("radio", "Test 1", 0, value));
        add.append(generateContent("radio", "Test 2", 1, value));
    }
    /*if (value == QuestionType.PARAGRAPH && block.find("textarea").length == 0) {
        var clazz = input.attr("class");
        var textbox = jQuery(document.createElement("textarea")).attr("class", clazz);
        input.replaceWith(textbox);
    }*/
}
