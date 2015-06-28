
App.service('Status', ['$http', function($http) {
    return {
        getData: function() {
            return  $http.get('/stats/');
        },

        deleteEvent: function(eventId) {
            return $http.delete('/stats/event/?job_id={0}'.format(eventId));
        },

        reset: function(key) {
            var url = '/stats/reset/'.format(key);

            return $http.post(url, {key: key})
        }
    }
}]);
