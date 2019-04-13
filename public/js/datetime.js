jQuery(document).ready(function () {
    jQuery(".datetimepicker").datetimepicker({
        locale: window.Laravel.lang
    });
    jQuery(".datetimepicker-button").click(function () {
        jQuery(this).closest(".input-group").find(".datetimepicker").focus();
    });
});