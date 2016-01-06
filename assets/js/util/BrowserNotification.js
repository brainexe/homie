
// todo refactor
App.service('BrowserNotification', ['$q', '_', function($q, _) {
    var TIMEOUT = 5000;

    function request() {
        return $q(function(resolve, reject) {
            if (!("Notification" in window)) {
                reject();
            } else if (Notification.permission === "granted") {
                resolve();
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission(function (permission) {
                    // Whatever the user answers, we make sure we store the information
                    if (!('permission' in Notification)) {
                        Notification.permission = permission;
                    }

                    // If the user is okay, let's create a notification
                    if (permission === "granted") {
                        resolve();
                    }
                });
            }
        });
    }

    var openNotifications = [];

    function show(content) {
        var notification = new Notification(_('Homie'), {
            body: content,
            icon: 'favicon.ico'
        });
        notification.$content = content;

        setTimeout(function() {
            notification.close();
            notification.$content = '';
        }, TIMEOUT);

        openNotifications.push(notification);
    }

    var contentQueue = [];

    return {
        show: function(content) {
            request().then(function() {
                contentQueue.push(content);

                window.setTimeout(function() {
                    if (contentQueue.length == 0) {
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
                }, 1500);
            });
        }
    }
}]);
