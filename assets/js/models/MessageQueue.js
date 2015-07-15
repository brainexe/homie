
App.service('MessageQueue', ['$http', function($http) {
    return {
        getJobs: function(type, futureOnly) {
            return $http.get('/jobs/{0}/{1}'.format(type, futureOnly ? '?future=1' : ''));
        },

        deleteJob: function(jobId) {
            return $http.delete('/stats/event/?job_id={0}'.format(jobId));
        }
    }
}]);
