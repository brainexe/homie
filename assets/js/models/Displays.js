
App.service('Displays', /*@ngInject*/ function($http) {
    return {
        getData: ()          => $http.get('/display/'),

        add:     (display)   => $http.post('/display/', display),

        update:  (display)   => $http.put(`/display/${display.displayId}/`, display),

        delete:  (displayId) => $http.delete(`/display/${displayId}/`),

        redraw:  (displayId) => $http.post(`/display/${displayId}/redraw/`, {})
    };
});
