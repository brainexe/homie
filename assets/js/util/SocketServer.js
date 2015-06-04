
App.service('SocketServer', ['Config', '$rootScope', function(Config, $rootScope) {
    Config.get('socketUrl', 'debug', function(socketUrl, debug) {
        if (!socketUrl) {
            return;
        }

        var sockjs = new SockJS(socketUrl);

        sockjs.onmessage = function (message) {
            var event      = JSON.parse(message.data);
            var event_name = event.event_name;

            rootScope.$broadcast(event_name, event);

            if (debug) {
                console.log("socket server: " + event.event_name, event)
            }
        };
    })
}]);
