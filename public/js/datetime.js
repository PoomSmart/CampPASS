jQuery(document).ready(function () {
    jQuery(".datetimepicker").datetimepicker({
        locale: window.Laravel.lang,
        format: 'Y-M-D hh:mm'
    });
    jQuery(".datetimepicker-button").click(function () {
        jQuery(this).closest(".input-group").find(".datetimepicker").focus();
    });
});