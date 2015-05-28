
App.ng.service('UserManagement.TOTP', ['$http', function($http) {
    return {
        getData: function () {
            return $http.get('/one_time_password/');
        },

        request: function () {
            return $http.post('/one_time_password/request/', {});
        },

        deleteToken: function () {
            return $http.delete('/one_time_password/');
        },

        needsToken: function (username) {
            return $http.get(
                '/login/needsOneTimeToken',
                {username: username}
            );
        },

        sendMail: function (username) {
            return $http.post('/one_time_password/mail/', {user_name: username});
        }
    };
}]);
