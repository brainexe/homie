
App.service("EggTimer", /*@ngInject*/ function($http) {
    return {
        JOB_ID: "egg_timer.done",

        setTimer (time, text) {
            var payload = {time, text};

            return $http.post("/egg_timer/", payload);
        }
    };
});
