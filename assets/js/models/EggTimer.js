
App.service('EggTimer', ['$http', function($http) {
    return {
        getJobs: function() {
            return $http.get('/egg_timer/');
        },

        setTimer: function (time, text) {
            var payload = {
                time: time,
                text: text
            };

            return $http.post('/egg_timer/', payload);
        },

        deleteTimer: function(jobId) {
            return $http.delete('/egg_timer/{0}/'.format(jobId));
        }
    }
}]);
