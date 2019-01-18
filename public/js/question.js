function addQuestion() {
    var block = jQuery("#question-block").first().clone().attr("id", `question-block`);
    block.find("#question-title").val("Title").attr("id", `question-title`);
    block.find("label").val("Question").attr("for", `question`);
    block.find("input").attr("id", `question`);
    jQuery("#questions").append(block);
}

function deleteQuestion(button) {
    $(button).closest("#question-block").remove();
}