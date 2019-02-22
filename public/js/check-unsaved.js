jQuery(document).ready(function (){
    var form = $('#form'), original = form.serialize();
    form.submit(function () {
        window.onbeforeunload = null;
    });
    window.onbeforeunload = function () {
        if (form.serialize() != original)
            return true;
    };
});