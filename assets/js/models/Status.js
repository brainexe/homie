
App.service('Status', /*@ngInject*/ function($http) {
    return {
        getData:        ()          => $http.get('/stats/'),
        deleteJob:      (eventId)   => $http.delete(`/jobs/${eventId}/`),
        forceJob:       (eventId)   => $http.post(`/jobs/force/${eventId}/`, {}),
        reset:          (key)       => $http.post('/stats/reset/', {key})
    };
});
