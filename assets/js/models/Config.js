
App.service('Config', /*@ngInject*/ function($http, Cache) {
    return {
        getAll: function getAll() {
            return $http.get('/config/', {cache: Cache});
        }
    }
});
