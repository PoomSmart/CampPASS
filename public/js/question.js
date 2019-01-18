function addQuestion() {
    var count = jQuery("[id^=question-block]").length;
    var block = jQuery("#question-block-1").first().clone().attr("id", `question-block-${count + 1}`);
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

function selectionChanged(select) {
    var value = parseInt(select.value);
    var block = jQuery(select).closest("[id^=question-block]");
    var input = block.find("#question");
    if (value == 1 && block.find("input[type=text]").length == 0) {

    }
    /*if (value == 2 && block.find("textarea").length == 0) {
        var clazz = input.attr("class");
        var textbox = jQuery(document.createElement("textarea")).attr("class", clazz);
        input.replaceWith(textbox);
    }*/
}
