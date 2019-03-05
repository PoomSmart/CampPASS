
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

var notifications = [];

const NOTIFICATION_TYPES = {
    application_status: 'App\\Notifications\\ApplicationStatusUpdated',
    new_camp: 'App\\Notifications\\NewCampRegistered'
};

jQuery(document).ready(function () {
    if (Laravel.userId) {
        jQuery.get('/profile/notifications', function (data) {
            addNotifications(data, "#notifications");
        });
    }
});

function addNotifications(newNotifications, target) {
    notifications = _.concat(notifications, newNotifications);
    notifications.slice(0, 5);
    showNotifications(notifications, target);
}

function showNotifications(notifications, target) {
    if (notifications.length) {
        var htmlElements = notifications.map(function (notification) {
            return makeNotification(notification);
        });
        jQuery(target + 'Menu').html(htmlElements.join(''));
        jQuery(target).addClass('has-notifications')
    } else {
        jQuery(target + 'Menu').html(`<li class="dropdown-header">${Laravel.no_notification_text}</li>`);
        jQuery(target).removeClass('has-notifications');
    }
}

function makeNotification(notification) {
    var to = routeNotification(notification);
    var notificationText = makeNotificationText(notification);
    return `<li class="nav-link"><a class="nav-link" target="_blank" href="${to}">${notificationText}</a></li>`;
}

function routeNotification(notification) {
    var to = '?read=' + notification.id;
    switch (notification.type) {
        case NOTIFICATION_TYPES.application_status:
            to = `application/status/${notification.data.registration_id}${to}`;
            break;
        case NOTIFICATION_TYPES.new_camp:
            to = `camps/${notification.data.camp_id}${to}`;
            break;
    }
    return '/' + to;
}

function makeNotificationText(notification) {
    var text = '';
    const content = notification.data.content;
    text += '<strong>' + content + '</strong>';
    return text;
}