
App.service('UserManagement', ['$http', function($http) {
    var current = {};

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

        setCurrentUser: function (user) {
            current = user;
        },

        getCurrentUser: function () {
            return current;
        },

        isLoggedIn: function() {
            return current && current.id > 0;
        },

        loadCurrentUser: function () {
            var promise = $http.get('/user/');

            promise.success(function(user) {
                current = user;
            });

            return promise;
        },

        changePassword: function(password) {
            return $http.post('/user/change_password/', {password:password});
        }
    };
}]);
