
App.ng.service('Status', ['$http', function($http) {
    return {
        getData: function() {
            return  $http.get('/stats/');
        },

        deleteEvent: function(eventId) {
            return $http.post('/stats/event/delete/', {job_id: eventId});
        },

        reset: function(key) {
            var url = '/stats/reset/'.format(key);

            return $http.post(url, {key: key})
        }
    }
}]);
