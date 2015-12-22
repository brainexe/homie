
App.service('UserManagement.Settings', ['$http', 'Cache', function($http, Cache) {
    return {
        getAll: function() {
            return $http.get('/settings/', {cache:Cache});
        },

        set: function(key, value) {
            Cache.intervalClear('^/settings/$', 60);
            return $http.post('/settings/{0}/{1}/'.format(key, value));
        }
    };
}]);
