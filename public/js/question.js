i = 2;

function addQuestion() {
    var block = jQuery("#question-block-1").first().clone().attr("id", `question-block-${i}`);
    block.find("#question-title-1").val("Title").attr("id", `question-title-${i}`);
    block.find("label").val("Question").attr("for", `question_${i}`);
    block.find("input").attr("id", `question_${i}`);
    ++i;
    jQuery("#questions").append(block);
}