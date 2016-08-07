
App.service('Sensor', /*@ngInject*/ function($http, $rootScope, Cache) {
    $rootScope.$on('sensor.value', function(event, data) {
        clearCache();
        $rootScope.$broadcast('sensor.update', data.sensorVo);
    });

    function clearCache() {
        Cache.clear('^/sensors/');

    }

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
            clearCache();

            return $http.delete('/sensors/{0}/'.format(sensorId));
        },

        deleteValue: function(sensorId, values) {
            clearCache();

            return $http.delete('/sensors/{0}/values/{1}/'.format(sensorId, values));
        },

        addValue: function(sensorId, value) {
            clearCache();

            return $http.post('/sensors/{0}/value/'.format(sensorId), {value:value});
        },

        forceReadValue: function(sensorId) {
            clearCache();

            return $http.post('/sensors/{0}/force/'.format(sensorId), {});
        },

        edit: function(sensor) {
            clearCache();

            return $http.put('/sensors/{0}/'.format(sensor.sensorId), sensor);
        },

        add: function(sensor) {
            clearCache();

            return $http.post('/sensors/', sensor);
        },

        parameters: function(sensorType) {
            return $http.get('/sensors/{0}/parameters/'.format(sensorType), {cache: Cache});
        }
    };
});
