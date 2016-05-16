
App.service('Gpio', ['$http', function($http) {
    return {
        getData: function(nodeId) {
            return $http.get('/gpio/{0}/'.format(nodeId));
        },

        setDescription: function(nodeId, pin, description) {
            return $http.post(
                '/gpio/description/',
                {
                    pinId: pin,
                    nodeId: nodeId,
                    description: description
                }
            );
        },

        savePin: function(nodeId, pin, direction, value) {
            var url = '/gpio/set/{0}/{1}/{2}/{3}/'.format(nodeId, ~~pin, ~~direction, ~~value);
            return $http.post(url, {});
        }
    }
}]);
