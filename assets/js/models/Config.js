
App.service('Config', ['$q', '$http', function($q, $http) {
    var CACHE_KEY = 'config';
    var cache     = null;

    if (localStorage.getItem(CACHE_KEY)) {
        cache = JSON.parse(localStorage.getItem(CACHE_KEY));
    }

    function getAll() {
        return $q(function(resolve, reject) {
            if (cache != null) {
                resolve(cache);
            }

            $http.get('/config/', {cache:true}).success(function(config) {
                resolve(config);
                cache = config;
                localStorage.setItem(CACHE_KEY, JSON.stringify(config));
            })
        });
    }

    return {
        get: function() {
            var keys = arguments;

            return $q(function(resolve, reject) {
                getAll().then(function (all) {
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
