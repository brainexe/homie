
App.service('Webcam', /*@ngInject*/ function($http) {
    return {
        getData: function() {
            return $http.get('/webcam/');
        },

        takeShot: function() {
            return $http.post('/webcam/photo/', {});
        },

        getRecent: function() {
            return $http.get('/webcam/recent/');
        },

        takeVideo: function(duration) {
            return $http.post('/webcam/video/', {duration:duration});
        },

        takeSound: function(duration) {
            return $http.post('/webcam/sound/', {duration:duration});
        },

        remove: function(shotId) {
           return $http.delete('/webcam/?shotId={0}'.format(shotId));
        }
    }
});
