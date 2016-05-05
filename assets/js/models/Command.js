
App.service('Command', ['$http', function($http) {
    return {
        execute: function(command) {
            return $http.post('/command/', {
                command: command
            });
        }
    }
}]);
