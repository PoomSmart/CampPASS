require('./bootstrap');

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

var notifications = [];

jQuery(document).ready(function () {
    if (Laravel.userId) {
        jQuery.get('/notifications/notifications', function (data) {
            addNotifications(data, "#notifications");
        });
    }
    jQuery('#app').on('change keyup keydown paste cut', 'textarea', function () {
        jQuery(this).height(0).height(this.scrollHeight);
    }).find('textarea').change();
    jQuery('#sidenav .nav-link').on('click', function () {
        jQuery('#sidenav').find('.nav-link.active').removeClass('active');
        jQuery(this).addClass('active');
    });
});

function addNotifications(newNotifications, target) {
    notifications = _.concat(notifications, newNotifications);
    notifications.slice(0, 5);
    showNotifications(notifications, target);
}

function showNotifications(notifications, target) {
    var menu = jQuery(target + 'Menu');
    if (notifications.length) {
        notifications.forEach(function (notification) {
            menu.append(jQuery.parseHTML(makeNotification(notification)));
        });
        jQuery(target).addClass('has-notifications')
    } else {
        menu.html(`<li class="dropdown-header">${Laravel.no_notification_text}</li>`);
        jQuery(target).removeClass('has-notifications');
    }
}

function makeNotification(notification) {
    var to = routeNotification(notification);
    var notificationText = makeNotificationText(notification);
    return `<li class="nav-link ml-2"><a href="${to}">${notificationText}</a></li>`;
}

function routeNotification(notification) {
    return `${notification.data.url}?read=${notification.id}`;
}

window.routeNotification = routeNotification;

function makeNotificationText(notification) {
    var text = '';
    const content = notification.data.content[`${window.Laravel.lang}`];
    text += '<strong>' + content + '</strong>';
    return text;
}