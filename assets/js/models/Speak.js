
App.ng.service('Speak', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/espeak/');
        },

        speak: function (payload) {
            return $http.post('/espeak/speak/', payload);
        },

        deleteJob: function(jobId) {
            return $http.delete('/espeak/job/{0}/', jobId);
        }
    };
}]);
