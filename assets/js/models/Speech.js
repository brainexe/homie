
App.service('Speech', /*@ngInject*/ function($http) {
    return {
        sendText: function (text) {
            var payload = {
                text: text
            };

            return $http.post('/speech/', payload);
        }
    }
});
