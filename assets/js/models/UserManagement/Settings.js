
App.service('UserManagement.Settings', ['$http', 'Cache', function($http, Cache) {
    // todo implement some generic solution
    var metaData = {
        'browserNotification' : {},
        'hiddenMenus' : {},
        'espeakBrowserOutput': {},
        'voiceControl': {},
        'selectedDashboardId': {}
    };

    return {
        getAll: function() {
            return $http.get('/settings/', {cache:Cache});
        },

        set: function(key, value) {
            Cache.clear('^/settings/');

            return $http.post('/settings/{0}/'.format(key), {
                value: value
            });
        }
    };
}]);
