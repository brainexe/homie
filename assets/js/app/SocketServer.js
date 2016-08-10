
App.run(/*@ngInject*/ function(Config, $rootScope) {
    $rootScope.$on('currentuser.authorized', function () {
        Config.getAll().success(function(config) {
            if (!config.socketUrl) {
                return;
            }

            var sockjs = new SockJS(config.socketUrl);
            sockjs.onmessage = function(message) {
                var event     = JSON.parse(message.data);
                var eventName = event.eventName;

                $rootScope.$broadcast(eventName, event);

                console.log("socket server: " + event.eventName, event)
            };
        });
    });
});
