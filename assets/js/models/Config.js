
App.service('Config', ['$q', '$http', 'Cache', function($q, $http, Cache) {
    function getAll() {
        return $http.get('/config/', {cache:Cache});
    }

    return {
        get: function() {
            var keys = arguments;

            return $q(function(resolve, reject) {
                getAll().success(function (all) {
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
