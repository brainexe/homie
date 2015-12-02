
App.service('Speak', ['$http', 'Cache', function($http, Cache) {
    return {
        JOB_ID: 'espeak.speak',

        getSpeakers: function() {
            return $http.get('/espeak/speakers/', {
                cache: Cache
            });
        },

        speak: function (payload) {
            return $http.post('/espeak/speak/', payload);
        }
    };
}]);
