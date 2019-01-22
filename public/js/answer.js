function readJSON(json) {
    if (!json) {
        console.log("Info: JSON is null");
        return
    }
    var old_block = jQuery(question_block_selector).first();
    Object.keys(json.question).forEach(function(key) {
        
        jQuery("#questions").append(block);
    });
}