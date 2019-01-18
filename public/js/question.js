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
    $(button).closest("[id^=question-block]").remove();
    return false;
}