
App.service('UserManagement', ['$http', '$rootScope', 'Cache', function($http, $rootScope, Cache) {
    // clear the user in cache initially
    Cache.clear('^/user/$');
    Cache.intervalClear('^/user/$', 60);

    var current = {};
    var setCurrentUser;
    var loadUserPromise;

    return {
        register: function(payload) {
            Cache.clear('^/user/$');
            return $http.post('/register/', payload)
        },

        logout: function() {
            Cache.clear('^/user/$');
            return $http.post('/logout/', {});
        },

        login: function(payload) {
            Cache.clear('^/user/$');
            return $http.post('/login/', payload).success(function() {
                Cache.clear('^/user/$');
                UserManagement.setCurrentUser(result);
            });
        },

        list: function() {
            return $http.get('/user/list/', {cache:Cache});
        },

        setCurrentUser: setCurrentUser = function (user) {
            if (current.userId != user.userId) {
                $rootScope.$broadcast('currentuser.update', user);
            }

            current = user;
        },

        isLoggedIn: function(user) {
            user = user || current;
            return user && user.userId > 0;
        },

        loadCurrentUser: function () {
            if (loadUserPromise) {
                return loadUserPromise;
            }

            var promise = loadUserPromise = $http.get('/user/', {cache:Cache});

            promise.success(function(user) {
                setCurrentUser(user);
                loadUserPromise = null;
            });

            return promise;
        },

        changePassword: function(oldPassword, newPassword) {
            Cache.clear('^/user/$');

            return $http.post('/user/change_password/', {
                oldPassword: oldPassword,
                newPassword: newPassword
            });
        },

        changeEmail: function(email) {
            Cache.clear('^/user/$');
            return $http.post('/user/change_email/', {email:email});
        }
    };
}]);
