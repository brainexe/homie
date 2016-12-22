
App.service('UserManagementSettings', /*@ngInject*/ function($http, Cache) {
    return {
        getAll: function() {
            return $http.get('/settings/', {cache: Cache});
        },

        set (key, value) {
            Cache.clear('^/settings/');

            return $http.post(`/settings/${key}/`, {value});
        }
    };
});
