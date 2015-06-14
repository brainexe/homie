
App.service('Sensor', ['$http', function($http) {
    return {
        getAll: function() {
            return $http.get('/sensors/');
        },

        getValues: function(sesnorsIds, parameters) {
            parameters = parameters || '';

            return $http.get('/sensors/load/{0}/{1}'.format(sesnorsIds, parameters))
        },

        getSensorData: function(sensorId) {
            return $http.get('/sensors/{0}/value/'.format(sensorId));
        },

        deleteSensor: function(sensorId) {
            return $http.delete('/sensors/{0}/'.format(sensorId));
        },

        edit: function(sensor) {
            return $http.put('/sensors/{0}/'.format(sensor.sensorId), sensor);
        }
    };
}]);
