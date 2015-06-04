
App.service('Sensor', ['$http', function($http) {
    return {
        getAll: function() {
            return $http.get('/sensors/');
        },

        getValues: function(parameters) {
            return $http.get('/sensors/load/{0}/'.format(parameters))
        },

        getSensorData: function(sensorId) {
            return $http.get('/sensors/{0}/value/'.format(sensorId));
        },

        deleteSensor: function(sensorId) {
            return $http.delete('/sensors/{0}/'.format(sensorId));
        }
    };
}]);
