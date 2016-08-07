
App.service('UserManagement', ["$http", "$rootScope", "Cache", function($http, $rootScope, Cache) {
    function clearCache() {
        Cache.clear('^/user/$');
    }

    // clear the user in cache initially
    clearCache();
    Cache.intervalClear('^/user/$', 60);

    var current = {};
    var setCurrentUser;
    var loadUserPromise;

    return {
        register: function(payload) {
            clearCache();
            return $http.post('/register/', payload)
        },

        logout: function() {
            clearCache();
            return $http.post('/logout/', {});
        },

        login: function(payload) {
            clearCache();
            return $http.post('/login/', payload).success(function(result) {
                clearCache();
                setCurrentUser(result);
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
            clearCache();

            return $http.post('/user/change_password/', {
                oldPassword: oldPassword,
                newPassword: newPassword
            });
        },

        changeEmail: function(email) {
            clearCache();

            return $http.post('/user/change_email/', {email:email});
        }
    };
}]);
