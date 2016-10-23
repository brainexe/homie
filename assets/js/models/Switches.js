
App.service("Switches", /*@ngInject*/ function($http) {
    const BASE_URL = "/switches/";

    return {
        JOB_ID:     "switch.change",

        getData:    ()                  => $http.get(BASE_URL),
        add:        (newSwitch)         => $http.post(BASE_URL, newSwitch),
        setStatus:  (switchId, status)  => $http.post(BASE_URL + `${switchId}/status/${status}/`, {}),
        delete:     (switchId)          => $http.delete(BASE_URL + `${switchId}/`),
        addJob:     (newJob)            => $http.post(BASE_URL + 'jobs/', newJob),
        deleteJob:  (eventId)           => $http.delete(BASE_URL + `jobs/${eventId}/`),

        getDataCached: () =>
            $http.get(BASE_URL, {
                cache: true
            })
    };
});
