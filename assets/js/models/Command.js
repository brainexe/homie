
App.service('Command', /*@ngInject*/ function($http) {
    return {
        execute: function(command) {
            return $http.post('/command/', {
                command: command
            });
        }
    }
});
