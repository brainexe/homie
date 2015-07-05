
App.service('Config', ['$q', '$http', 'Cache', '$rootScope', function($q, $http, Cache, $rootScope) {
    function getAll() {
        return $http.get('/config/', {cache:Cache});
    }

    // todo cleaner solution
    $rootScope.config = function(key) {
        return null;
    };

    return {
        get: function() {
            var keys = arguments;

            return $q(function(resolve, reject) {
                getAll().then(function (all) {
                    $rootScope.config = function(key) {
                        return all[key];
                    };

                    var values = [];
                    for (var i in keys) {
                        values.push(all[keys[i]])
                    }

                    resolve.apply(this, values);
                });
            });
        },

        getAll: getAll
    }
}]);
