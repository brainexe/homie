
App.service('Help', ['$http', function($http) {
    return {
        getAll: function() {
            return $http.get('/help/');
        },

        save: function(type, value) {
            return $http.post('/help/{0}/'.format(type), {content:value});
        },

        delete: function(type) {
            return $http.delete('/help/{0}/'.format(type));
        }
    }
}]);
