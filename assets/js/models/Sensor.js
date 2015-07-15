
App.service('Sensor', ['$http', '$rootScope', 'Cache', function($http, $rootScope, Cache) {
    $rootScope.$on('sensor.value', function(event, data) {
        Cache.clear('/sensors/.*');
        $rootScope.$broadcast('sensor.update', data.sensorVo);
    });

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

        getSensorData: function(sensorId, cached) {
            return $http.get('/sensors/{0}/value/'.format(sensorId, {cache: cached && Cache}));
        },

        deleteSensor: function(sensorId) {
            Cache.clear('^/sensors/.*');
            return $http.delete('/sensors/{0}/'.format(sensorId));
        },

        addValue: function(sensorId, value) {
            return $http.post('/sensors/{0}/value/'.format(sensorId), {value:value});
        },

        forceReadValue: function(sensorId) {
            return $http.post('/sensors/{0}/force/'.format(sensorId), {});
        },

        edit: function(sensor) {
            Cache.clear('^/sensors/.*');
            return $http.put('/sensors/{0}/'.format(sensor.sensorId), sensor);
        }
    };
}]);
