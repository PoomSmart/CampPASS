var title, content;

function readContent(t, c) {
    title = t;
    content = c;
}

jQuery(document).ready(function () {
    jQuery('[data-toggle="status-popover"]').popover({
        title: title,
        content: content,
        html: true
    });
    jQuery('[data-toggle="status"]').tooltip();
});