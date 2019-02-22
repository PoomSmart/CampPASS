jQuery(document).ready(function () {
    jQuery("#modal").on("show.bs.modal", function (event) {
        var target = jQuery(event.relatedTarget);
        var form_action = target.attr("data-action");
        jQuery(this).find("#confirm-form").attr("action", form_action);
    });
});