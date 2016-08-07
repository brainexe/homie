
App.service('MessageQueue', /*@ngInject*/ function($http) {
    return {
        JOBS_HANDLED: 'message_queue.handled',

        getJobs: function(type, futureOnly) {
            return $http.get('/jobs/{0}/{1}'.format(type, futureOnly ? '?future=1' : ''));
        },

        deleteJob: function(jobId) {
            return $http.delete('/jobs/{0}/'.format(jobId));
        }
    }
});
