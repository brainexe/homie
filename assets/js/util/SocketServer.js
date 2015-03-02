
App.SocketServer = {

    /**
     * @param {String} socketUrl
     */
    connect: function (socketUrl) {
        var sockjs = new SockJS(socketUrl);

        sockjs.onmessage = function (message) {
            var event      = JSON.parse(message.data);
            var event_name = event.event_name;

            App.Layout.$scope.$broadcast(event_name, event);

            if (App.Layout.debug) {
                console.log("socket server: " + event.event_name, event)
            }
        };
    }
};
