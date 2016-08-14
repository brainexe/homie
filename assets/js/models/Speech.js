
App.service('Speech', /*@ngInject*/ function($http) {
    return {
        sendText: (text) => $http.post('/speech/', {text})
    };
});
