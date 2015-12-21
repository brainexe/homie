
App.service('Gpio', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/gpio/');
        },

        setDescription: function(pin, description) {
            return $http.post(
                '/gpio/description/',
                {
                    pinId: pin,
                    description: description
                }
            );
        },

        savePin: function(pin, direction, value) {
            var url = '/gpio/set/{0}/{1}/{2}/'.format(~~pin, ~~direction, ~~value);
            return $http.post(url, {});
        }
    }
}]);
