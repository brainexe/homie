
App.ng.service('UserManagement', ['$http', function($http) {
    return {
        register: function(payload) {
            return $http.post('/register/', payload)
        },

        logout: function() {
            return $http.post('/logout/', {});
        },

        login: function(payload) {
            return $http.post('/login/', payload);
        },

        getCurrentUser: function () {
            return $http.get('/user/');
        },

        changePassword: function(password) {
            return $http.post('/user/change_password/', {password:password});
        },
    };
}]);
