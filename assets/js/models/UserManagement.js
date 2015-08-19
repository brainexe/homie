
App.service('UserManagement', ['$http', 'Cache', function($http, Cache) {
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

        list: function() {
            return $http.get('/user/list/', {cache:Cache});
        },

        setCurrentUser: function (user) {
            current = user;
        },

        getCurrentUser: function () {
            return current;
        },

        isLoggedIn: function(user) {
            user = user || current;
            return user && user.id > 0;
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
