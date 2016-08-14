
App.service('BrowserNotification', ["$q", "$timeout", "_", function($q, $timeout, _) {
    var CLOSE_DELAY = 5000;
    var QUEUE_DELAY =  500;

    var PERMISSION_DENIED  = 'denied';
    var PERMISSION_GRANTED = 'granted';

    var openNotifications = [];
    var contentQueue      = [];

    var GlobalNotification = window.Notification;

    function requestPermission() {
        return $q(function(resolve, reject) {
            if (!GlobalNotification) {
                reject();
            } else if (GlobalNotification.permission === PERMISSION_GRANTED) {
                resolve();
            } else if (GlobalNotification.permission !== PERMISSION_DENIED) {
                GlobalNotification.requestPermission(function (permission) {
                    // Whatever the user answers, we make sure we store the information
                    if (!('permission' in GlobalNotification)) {
                        GlobalNotification.permission = permission;
                    }

                    // If the user is okay, let's create a notification
                    if (permission === PERMISSION_GRANTED) {
                        resolve();
                    }
                });
            }
        });
    }

    function show(content) {
        var notification = new GlobalNotification(_('Homie'), {
            body: content,
            icon: '/images/homie144.png'
        });
        notification.$content = content;

        $timeout(function() {
            notification.close();
            notification.$content = '';
        }, CLOSE_DELAY);

        openNotifications.push(notification);
    }

    return {
        show (content) {
            console.debug('Add browser notification: ' + content);

            requestPermission().then(function() {
                contentQueue.push(content);

                $timeout(function() {
                    if (!contentQueue.length) {
                        // content already shown
                        return;
                    }

                    // close all other open notification and replace by extended one
                    if (openNotifications.length) {
                        var notification;

                        while (notification = openNotifications.pop()) {
                            if (notification.$content) {
                                contentQueue.push(notification.$content);
                                notification.$content = '';
                            }
                            notification.close();
                        }
                    }
                    show(contentQueue.join("\n"));
                    contentQueue = [];
                }, QUEUE_DELAY);
            });
        }
    };
}]);
