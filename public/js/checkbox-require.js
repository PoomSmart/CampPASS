jQuery(document).ready(function () {
    var checkboxes = jQuery("input:checkbox");
    checkboxes.on("change", function () {
        var self = jQuery(this);
        if (self.is(":checked"))
            self.attr("required", "required");
        else
            self.removeAttr("required");
        jQuery("input:checkbox:not(:checked)").not(this).removeAttr("required");
    });
    jQuery("input:checkbox:not(:checked)").removeAttr("required");
});