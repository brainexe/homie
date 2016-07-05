
App.service('UserManagement.Tokens', ['$http', function($http) {
    return {
        getData: function () {
            return $http.get('/user/tokens/');
        },

        add: function(roles, name) {
            return $http.post('/user/tokens/', {
                roles: roles,
                name: name
            });
        },

        deleteToken: function(token) {
            return $http.delete('/user/tokens/{0}/'.format(token));
        }
    };
}]);
