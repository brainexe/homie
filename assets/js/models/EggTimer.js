
App.service('EggTimer', ['$http', 'Cache', function($http, Cache) {
    return {
        JOB_ID: 'egg_timer.done',

        getData: function() {
            return $http.get('/egg_timer/', {cache: Cache});
        },

        setTimer: function (time, text) {
            var payload = {
                time: time,
                text: text
            };

            return $http.post('/egg_timer/', payload);
        }
    }
}]);
