
App.service('SocketServer', ['Config', '$rootScope', function(Config, $rootScope) {
    Config.get('socketUrl', 'debug').then(function(config) {
        var socketUrl = config[0];
        if (!socketUrl) {
            return;
        }
        var debug  = config[1];
        var sockjs = new SockJS(socketUrl);
        sockjs.onmessage = function(message) {
            var event     = JSON.parse(message.data);
            var eventName = event.eventName;

            $rootScope.$broadcast(eventName, event);

            if (debug) {
                console.log("socket server: " + event.eventName, event)
            }
        };
    });
}]);
