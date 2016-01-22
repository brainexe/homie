
App.service('UserManagement.Settings', ['$http', 'Cache', function($http, Cache) {
    var metaData = {
        'browserNotification' : {},
        'hiddenMenus' : {}
    };

    return {
        getAll: function() {
            return $http.get('/settings/', {cache:Cache});
        },

        set: function(key, value) {
            Cache.intervalClear('^/settings/$', 60);
            return $http.post('/settings/{0}/'.format(key), {
                value: value
            });
        }
    };
}]);
