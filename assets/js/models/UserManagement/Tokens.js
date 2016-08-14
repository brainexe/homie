
App.service('UserManagementTokens', /*@ngInject*/ function($http) {
    return {
        getData () {
            return $http.get('/user/tokens/');
        },

        add (roles, name) {
            return $http.post('/user/tokens/', {roles, name});
        },

        deleteToken (token) {
            return $http.delete(`/user/tokens/${token}/`);
        }
    };
});
