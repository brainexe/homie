
App.service('UserManagementSettings', /*@ngInject*/ function($http, Cache) {
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
});
