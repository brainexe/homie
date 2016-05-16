
App.service('Config', ['$http', 'Cache', function($http, Cache) {
    return {
        getAll: function getAll() {
            return $http.get('/config/', {cache: Cache});
        }
    }
}]);
