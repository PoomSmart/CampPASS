jQuery(document).ready(function () {
    jQuery('[data-toggle="popover"]').popover({
        title: "{!! trans('registration.Status') !!}",
        content: "<b>{!! trans('registration.CONFIRMED') !!}</b><br />{!! trans('registration.CONFIRMEDInfo') !!}<br/>\
        <b>{!! trans('registration.APPROVED') !!}</b><br/>{!! trans('registration.APPROVEDInfo') !!}<br/>\
        <b>{!! trans('registration.INTERVIEWED') !!}</b><br/>{!! trans('registration.INTERVIEWEDInfo') !!}<br/>\
        <b>{!! trans('registration.CHOSEN') !!}</b><br/>{!! trans('registration.CHOSENInfo') !!}<br/>\
        <b>{!! trans('registration.WITHDRAWED') !!}</b><br/>{!! trans('registration.WITHDRAWEDInfo') !!}<br/>\
        <b>{!! trans('registration.REJECTED') !!}</b><br/>{!! trans('registration.REJECTEDInfo') !!}<br/>",
        html: true
    });
    jQuery('[data-toggle="status"]').tooltip();
});