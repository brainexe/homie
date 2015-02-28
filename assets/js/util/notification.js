App.Notification = {
    /**
     * @param {String} content
     */
    show: function(content) {
        if (!("Notification" in window)) {
            // not supported
        } else if (Notification.permission === "granted") {
            var notification = new Notification(content, {
                icon: asset('favicon.ico')
            });
            window.setTimeout(function () {
                notification.close();
            }, 10000);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {

                // Whatever the user answers, we make sure we store the information
                if (!('permission' in Notification)) {
                    Notification.permission = permission;
                }

                // If the user is okay, let's create a notification
                if (permission === "granted") {
                    var notification = new Notification(content);
                }
            });
        }
    }
};
