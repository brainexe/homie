
App.service('Sensor', ['$http', '$q', function($http, $q) {
    return {
        getAll: function() {
            return $http.get('/sensors/');
        },

        /**
         * @returns Array
         */
        getAllMetadata: function() {
            return $q(function(resolve, reject) {
                $http.get('/sensors/', {cache:true}).success(function(data) {
                    resolve(data.types);
                });
            });
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
