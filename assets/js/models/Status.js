
App.service('Status', /*@ngInject*/ function($http) {
    return {
        getData:        ()          => $http.get('/stats/'),
        deleteEvent:    (eventId)   => $http.delete(`/jobs/${eventId}/`),
        reset:          (key)       => $http.post('/stats/reset/', {key})
    };
});
