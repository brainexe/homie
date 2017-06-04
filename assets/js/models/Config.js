
App.service('Config', /*@ngInject*/ function($http, Cache, lodash) {
    return {
        getAll: lodash.once(
            () => $http.get('/config/', {cache: Cache}).then(result => result.data)
        )
    };
});
