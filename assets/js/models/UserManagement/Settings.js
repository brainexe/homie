
App.service('UserManagementSettings', /*@ngInject*/ function($http, Cache) {
    return {
        getAll: () => $http.get('/settings/', {cache:Cache}),

        set (key, value) {
            Cache.clear('^/settings/');

            return $http.post(`/settings/${key}/`, {value});
        }
    };
});
