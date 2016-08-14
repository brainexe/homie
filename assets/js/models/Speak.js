
App.service('Speak', /*@ngInject*/ function($http, Cache) {
    return {
        JOB_ID: 'espeak.speak',

        getSpeakers: () =>
            $http.get('/espeak/speakers/', {
                cache: Cache
            }),

        speak: (payload) => $http.post('/espeak/speak/', payload)
    };
});
