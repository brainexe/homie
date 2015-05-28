
App.ng.service('Sensor', ['$http', function($http) {
    return {

        getAll: function() {
            return $http.get('/sensors/');
        },

        getValues: function(parameters) {
            return $http.get('/sensors/load/{0}'.format(parameters))
        },

        getSensorData: function(sensorId) {
            return $http.get('/sensors/value/', {sensor_id: sensorId});
        },

        deleteSensor: function(sensorId) {
            return $http.post('/sensors/delete/', {
                sensorId: sensorId
            });
        }
    };
}]);
