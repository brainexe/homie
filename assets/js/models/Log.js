
App.service('Log', /*@ngInject*/ function ($http) {
    return {
        logError: function (message) {
            return $http.post('/log/error/', {
                message: message
            });
        }
    };
});
