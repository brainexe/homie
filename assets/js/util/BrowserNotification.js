
App.service('BrowserNotification', ['$q', function($q) {
    function request(callback) {
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

    return {
        show: function(content) {
            request().then(function() {
                var notification = new Notification(content, {
                    icon: asset('favicon.ico')
                });
                window.setTimeout(function () {
                    notification.close();
                }, 10000);
            });
        }
    }
}]);
