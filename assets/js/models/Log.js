
App.service('Log', ['$http', function ($http) {
    return {
        logError: function (message) {
            return $http.post('/log/error/', {
                message: message
            });
        }
    };
}]);
