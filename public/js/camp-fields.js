var CampProcedure = {
    WALK_IN: 1,
    QA: 2,
    DEPOSIT: 3,
    QA_DEPOSIT: 4,
    QA_INTERVIEW: 5,
    QA_INTERVIEW_DEPOSIT: 6,
};

function enable(element) {
    element.prop("disabled", false).attr("required", "required");
}

function disable(element) {
    element.prop("disabled", true).val("").removeAttr("required");
}

function selectionChanged(select) {
    var value = parseInt(select.value);
    var form = jQuery(select).closest("form");
    var descs = form.find("[id^=camp_procedure_id-desc-inline]");
    if (typeof value == 'number' && !isNaN(value)) {
        var deposit = form.find("#deposit");
        var fee = form.find("#application_fee");
        var interview_date = form.find("#interview_date");
        var interview_info = form.find("#interview_information");
        var payment_info = form.find("#payment_information");
        var backup_limit = form.find("#backup_limit");
        // Deposit and Application Fee
        switch (value) {
            case CampProcedure.WALK_IN:
                disable(deposit);
                disable(payment_info);
                disable(fee);
                break;
            case CampProcedure.DEPOSIT:
            case CampProcedure.QA_DEPOSIT:
            case CampProcedure.QA_INTERVIEW_DEPOSIT:
                enable(deposit);
                enable(payment_info);
                disable(fee);
                break;
            default:
                disable(deposit);
                enable(payment_info);
                enable(fee);
                break;
        }
        // Interview
        switch (value) {
            case CampProcedure.QA_INTERVIEW:
            case CampProcedure.QA_INTERVIEW_DEPOSIT:
                enable(interview_date);
                enable(interview_info);
                break;
            default:
                disable(interview_date);
                disable(interview_info);
                break;
        }
        // Backup limit
        switch (value) {
            case CampProcedure.QA:
            case CampProcedure.QA_DEPOSIT:
            case CampProcedure.QA_INTERVIEW:
            case CampProcedure.QA_INTERVIEW_DEPOSIT:
                enable(backup_limit);
                break;
            default:
                disable(backup_limit);
                break;
        }
        descs.each(function(index, element) {
            var e = element.getAttribute("subvalue");
            if (e != value)
                jQuery(element).hide();
            else
                jQuery(element).show();
        });
    } else
        descs.hide();
}

jQuery(document).ready(function () {
    jQuery("[id^=camp_procedure_id-desc-inline]").hide();
    jQuery("#camp_procedure_id").change();
});