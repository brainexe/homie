
App.run(/*@ngInject*/ (Config, $rootScope) => {
    let sockjs = null;
    $rootScope.$on('currentuser.authorized', function (event, user) {
        Config.getAll().then(function(config) {
            if (sockjs || !config.socketUrl) {
                return;
            }
            sockjs = new SockJS(config.socketUrl);
            sockjs.onopen = function() {
                console.debug('connected to socket server');
                sockjs.send({action: 'auth', userId: user.userId})
            };

            sockjs.onclose = function() {
                console.debug('disconnected to socket server');
            };

            sockjs.onmessage = function(message) {
                let event     = JSON.parse(message.data);
                let eventName = event.eventName;

                $rootScope.$broadcast(eventName, event);

                console.log("socket server: " + event.eventName, event);
            };

            sockjs.onerror = function (error) {
                console.error(error)
            }
        });
    });
});
