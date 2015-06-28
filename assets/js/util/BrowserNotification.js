
App.service('BrowserNotification', ['$q', function($q) {
    var TIMEOUT = 10000; // 10s
    var notification,
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

    function startTimer() {
        timeout = window.setTimeout(function () {
            timeout = null;
            notification.close();
            notification = null;
        }, TIMEOUT);
    }
    return {
        show: function(content) {
            request().then(function() {
                if (false && notification) { // TODO extend existing?!
                    // extend!
                    window.clearTimeout(timeout);
                    startTimer();
                    notification.title += content;
                } else {
                    notification = new Notification(content, {
                        icon: asset('favicon.ico')
                    });
                    startTimer()
                }
            });
        }
    }
}]);
