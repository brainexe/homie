
App.service('Switches', /*@ngInject*/ function($http) {
    return {
        JOB_ID: 'switch.change',

        getData: function() {
            return $http.get('/switches/');
        },

        getDataCached: function () {
            return $http.get('/switches/', {
                cache: true
            });
        },

        add: function(newSwitch) {
            return $http.post('/switches/', newSwitch)
        },

        setStatus: function (switchId, status) {
            return $http.post('/switches/{0}/status/{1}/'.format(switchId, status), {});
        },

        delete: function(switchId) {
            return $http.delete('/switches/{0}/'.format(switchId));
        },

        addJob: function(newJob) {
            return $http.post('/switches/jobs/', newJob);
        },

        deleteJob: function(eventId) {
            return $http.delete('/switches/jobs/{0}/'.format(eventId))
        }
    }
});
