
App.service('Displays', /*@ngInject*/ function($http) {
    return {
        getData: function() {
            return $http.get('/display/');
        },

        add: function(display) {
            return $http.post('/display/', display);
        },

        update: function(display) {
            return $http.put('/display/{0}/'.format(display.displayId), display);
        },

        delete: function(displayId) {
            return $http.delete('/display/{0}/'.format(displayId));
        },

        redraw: function(displayId) {
            var url = '/display/{0}/redraw/'.format(displayId);

            return $http.post(url, {});
        }
    }
});
