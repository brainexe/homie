
App.service("Webcam", /*@ngInject*/ function($http) {
    return {
        getData:    ()          => $http.get("/webcam/"),
        takeShot:   ()          => $http.post("/webcam/photo/", {}),
        getRecent:  ()          => $http.get("/webcam/recent/"),
        takeVideo:  (duration)  => $http.post("/webcam/video/", {duration}),
        takeSound:  (duration)  => $http.post("/webcam/sound/", {duration}),
        remove:     (fileName)  => $http.delete(`/webcam/file/${fileName}`)
    };
});
