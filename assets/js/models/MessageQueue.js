
App.service('MessageQueue', /*@ngInject*/ function($http) {
    return {
        JOBS_HANDLED: 'message_queue.handled',

        getJobs (type, futureOnly = false) {
            futureOnly = futureOnly ? '?future=1' : '';

            return $http
                .get(`/jobs/${type}/${futureOnly}`)
                .then(result => result.data);
        },

        deleteJob (jobId) {
            return $http.delete(`/jobs/${jobId}/`);
        }
    };
});
