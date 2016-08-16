
App.service('UserManagement', ["$http", "$rootScope", "Cache", function($http, $rootScope, Cache) {
    function clearCache() {
        Cache.clear('^/user/$');
    }

    // clear the user in cache initially
    clearCache();
    Cache.intervalClear('^/user/$', 60 * 5);

    var current = {};
    var setCurrentUser;
    var loadUserPromise;

    var isLoggedIn = function(user) {
        user = user || current;

        return user && user.userId > 0;
    };

    return {
        register (payload) {
            clearCache();

            return $http.post('/register/', payload);
        },

        logout () {
            clearCache();

            return $http.post('/logout/', {});
        },

        login (payload) {
            clearCache();
            return $http.post('/login/', payload).success(function(result) {
                clearCache();
                setCurrentUser(result);
            });
        },

        list: () => $http.get('/user/list/', {cache: Cache}),

        setCurrentUser: setCurrentUser = function (user) {
            if (current.userId !== user.userId) {
                $rootScope.$broadcast('currentuser.update', user);
            }

            var oldLoggedIn = isLoggedIn();
            var newLoggedIn = isLoggedIn(user);

            if (!oldLoggedIn && newLoggedIn) {
                console.debug("Init user ", user);
                $rootScope.$broadcast('currentuser.authorized', user);
            } else if (oldLoggedIn && !newLoggedIn) {
                console.debug("Logged out user");
                $rootScope.$broadcast('currentuser.logout', user);
                clearCache();
            }

            current = user;
        },

        isLoggedIn,

        loadCurrentUser () {
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

        changePassword (oldPassword, newPassword) {
            clearCache();

            return $http.post('/user/change_password/', {oldPassword, newPassword});
        },

        changeEmail (email) {
            clearCache();

            return $http.post('/user/change_email/', {email});
        }
    };
}]);
