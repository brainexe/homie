
App.service('Log', /*@ngInject*/ function ($http) {
    return {
        logError: (message) =>
            $http.post('/log/error/', {message})
    };
});
