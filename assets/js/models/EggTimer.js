
App.service('EggTimer', /*@ngInject*/ function($http) {
    return {
        JOB_ID: 'egg_timer.done',

        setTimer: function (time, text) {
            var payload = {
                time: time,
                text: text
            };

            return $http.post('/egg_timer/', payload);
        }
    }
});
