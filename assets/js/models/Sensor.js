
App.service('Sensor', /*@ngInject*/ function($http, $rootScope, Cache) {
    function clearCache() {
        Cache.clear('^/sensors/');
    }

    $rootScope.$on('sensor.value', function(event, data) {
        clearCache();
        $rootScope.$broadcast('sensor.update', data.sensorVo);
    });

    return {
        getAll: () => $http.get('/sensors/'),

        getCachedData () {
            return $http
                .get('/sensors/', {cache: Cache})
                .then(result => result.data);
        },

        getValues (sensorsIds, parameters = '') {
            return $http.get(`/sensors/load/${sensorsIds}/${parameters}`);
        },

        getSensorData (sensorId, cached) {
            return $http.get(`/sensors/${sensorId}/value/`, {cache: cached && Cache});
        },

        deleteSensor (sensorId) {
            clearCache();

            return $http.delete(`/sensors/${sensorId}/`);
        },

        deleteValue (sensorId, values) {
            clearCache();

            return $http.delete(`/sensors/${sensorId}/values/${values}/`);
        },

        addValue (sensorId, value) {
            clearCache();

            return $http.post(`/sensors/${sensorId}/value/`, {value});
        },

        forceReadValue (sensorId) {
            clearCache();

            return $http.post(`/sensors/${sensorId}/force/`, {});
        },

        edit (sensor) {
            clearCache();

            return $http.put(`/sensors/${sensor.sensorId}/`, sensor);
        },

        add (sensor) {
            clearCache();

            return $http.post('/sensors/', sensor);
        },

        parameters (sensorType) {
            return $http.get(`/sensors/${sensorType}/parameters/`, {cache: Cache});
        }
    };
});
