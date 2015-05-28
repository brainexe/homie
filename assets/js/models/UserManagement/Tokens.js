
App.ng.service('UserManagement.Tokens', ['$http', function($http) {
    return {
        getData: function () {
            return $http.get('/user/tokens/');
        },

        add: function(roles) {
            return $http.post('/user/tokens/', {roles: roles});
        },

        deleteToken: function(token) {
            return $http.delete('/user/tokens/{0}'.format(token));
        }
    };
}]);
