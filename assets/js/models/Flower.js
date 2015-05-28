
App.ng.service('Flower', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/flower/');
        },

        water: function() {
            return  $http.post('/flower/water/', {});
        }
    }
}]);
