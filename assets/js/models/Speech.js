
App.service('Speech', ['$http', function($http) {
    return {
        sendText: function (text) {
            var payload = {
                text: text
            };

            return $http.post('/speech/', payload);
        }
    }
}]);
