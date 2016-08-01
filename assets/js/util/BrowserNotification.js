
// todo refactor
App.service('BrowserNotification', ['$q', '$timeout', '_', function($q, $timeout, _) {
    const CLOSE_DELAY = 5000;
    const QUEUE_DELAY = 1500;

    function requestPermission() {
        return $q(function(resolve, reject) {
            var notification = window.Notification;

            if (!notification) {
                reject();
            } else if (notification.permission === "granted") {
                resolve();
            } else if (notification.permission !== 'denied') {
                notification.requestPermission(function (permission) {
                    // Whatever the user answers, we make sure we store the information
                    if (!('permission' in notification)) {
                        notification.permission = permission;
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

        $timeout(function() {
            notification.close();
            notification.$content = '';
        }, CLOSE_DELAY);

        openNotifications.push(notification);
    }

    var contentQueue = [];

    return {
        show: function(content) {
            requestPermission().then(function() {
                contentQueue.push(content);

                $timeout(function() {
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
                }, QUEUE_DELAY);
            });
        }
    }
}]);
