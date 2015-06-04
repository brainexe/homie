
App.service('ShoppingList', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/todo/shopping/');
        },

        add: function(name) {
            return $http.post('/todo/shopping/', {name: name});
        },

        remove: function (name) {
            return $http.delete('/todo/shopping/', {name: name});
        }
    }
}]);
