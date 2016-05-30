
App.service('ShoppingList', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/shopping/');
        },

        add: function(name) {
            return $http.post('/shopping/', {name: name});
        },

        remove: function (name) {
            return $http.delete('/shopping/{0}/'.format(encodeURIComponent(name)));
        }
    }
}]);
