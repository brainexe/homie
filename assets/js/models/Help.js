
App.service('Help', ['$http', 'Cache', function($http, Cache) {
    return {
        getAll: function() {
            return $http.get('/help/', {cache: Cache});
        },

        save: function(type, value) {
            return $http.post('/help/{0}/'.format(type), {content:value});
        },

        delete: function(type) {
            return $http.delete('/help/{0}/'.format(type));
        }
    }
}]);
