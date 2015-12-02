
App.service('Status', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/stats/');
        },

        deleteEvent: function(eventId) {
            return $http.delete('/jobs/{0}/'.format(eventId));
        },

        reset: function(key) {
            var url = '/stats/reset/';

            return $http.post(url, {key: key})
        }
    }
}]);
