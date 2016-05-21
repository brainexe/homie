
App.service('Speech', ['$http', function($http) {
    return {
        sendEvent: function (text) {
            var payload = {
                text: text
            };

            return $http.post('/speech/', payload);
        }
    }
}]);
