
App.service('BrowserNotification', ['$q', '_', function($q, _) {
    var TIMEOUT = 10000;

    var currentNotification,
        currentContent,
        notification,
        timeout;

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

    var queue = [];

    function show(content) {
        var notification = new Notification(_('Homie'), {
            body: content,
            icon: asset('favicon.ico')
        });
        notification.$content = content;

        setTimeout(function() {
            notification.close();
        }, TIMEOUT);

        queue.push(notification);
    }

    return {
        show: function(content) {
            request().then(function() {
                if (queue.length) {
                    var notification;

                    while(notification = queue.pop()) {
                        content += "\n" + notification.$content;

                        notification.close();
                    }
                }
                show(content);
            });
        }
    }
}]);
