
App.ng.service('Webcam', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/webcam/');
        },

        takeShot: function() {
            return $http.post('/webcam/take/', {});
        },

        remove: function(shotId) {
           return $http.post('/webcam/delete/', {shotId: shotId});
        }
    }
}]);
