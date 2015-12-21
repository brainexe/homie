
App.service('UserManagement.Settings', ['$http', function($http) {
    return {
        getAll: function() {
            return $http.get('/settings/');
        },

        set: function(key, value) {
            return $http.put('/settings/{0}/{1}/'.format(key, value));
        }
    };
}]);
