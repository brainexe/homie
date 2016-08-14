
App.service('ShoppingList', /*@ngInject*/ function($http) {
    return {
        getData () {
            return $http.get('/shopping/');
        },

        add (name) {
            return $http.post('/shopping/', {name});
        },

        remove (name) {
            name = encodeURIComponent(name);
            return $http.delete(`/shopping/${name}/`);
        }
    };
});
