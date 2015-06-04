
App.service('Radios', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/radios/');
        },

        getDataCached: function () {
            return $http.get('/radios/', {
                cache: true
            });
        },

        add: function(newRadio) {
            return $http.post('/radios/', newRadio)
        },

        setRadio: function (radioId, status) {
            return $http.post('/radios/{0}/status/{1}/'.format(radioId, status), {});
        },

        deleteRadio: function(radioId) {
            return $http.delete('/radios/{0}/'.format(radioId));
        },

        addJob: function(newJob) {
            return $http.post('/radios/jobs/', newJob);
        },

        deleteJob: function(eventId) {
            return $http.delete('/radios/jobs/{0}/'.format(eventId))
        }
    }
}]);
