
App.service('Webcam', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/webcam/');
        },

        takeShot: function() {
            return $http.post('/webcam/', {});
        },

        remove: function(shotId) {
           return $http.delete('/webcam/', {shotId: shotId});
        }
    }
}]);
