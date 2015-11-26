
App.service('SocketServer', ['Config', '$rootScope', function(Config, $rootScope) {
    Config.get('socketUrl', 'debug').then(function(socketUrl, debug) {
        if (!socketUrl) {
            return;
        }

        var sockjs = new SockJS(socketUrl);

        sockjs.onmessage = function(message) {
            var event      = JSON.parse(message.data);
            var eventName = event.eventName;

            $rootScope.$broadcast(eventName, event);

            // todo why is debug "undefined"?
            if (debug) {
                console.log("socket server: " + event.eventName, event)
            }
        };
    });
}]);
