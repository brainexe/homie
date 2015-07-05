
App.service('Sensor', ['$http', 'Cache', function($http, Cache) {
    return {
        getAll: function() {
            return $http.get('/sensors/');
        },

        getCachedData: function() {
            return $http.get('/sensors/', {cache: Cache});
        },

        getValues: function(sensorsIds, parameters) {
            parameters = parameters || '';

            return $http.get('/sensors/load/{0}/{1}'.format(sensorsIds, parameters))
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
