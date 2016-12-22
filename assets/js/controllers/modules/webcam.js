App.controller('WebcamController', /*@ngInject*/ function ($scope, Webcam, Flash, _) {
    $scope.files = [];

    function update() {
        Webcam.getData().then(function (data) {
            $scope.files = data.data.files.map(function (entry) {
                entry.url = `/webcam/file/${entry.webPath}`;
                return entry;
            });
        });
    }
    update();

    $scope.$on('webcam.took_photo', update);
    $scope.$on('webcam.took_video', update);
    $scope.$on('webcam.took_sound', update);

    $scope.takeShot = function () {
        Webcam.takeShot().then(function() {
            Flash.addFlash(_('Cheese...'), Flash.INFO);
        });
    };

    $scope.takeVideo = function () {
        var duration = prompt(_('Duration'));
        Webcam.takeVideo(duration);
    };

    $scope.takeSound = function () {
        var duration = prompt(_('Duration'));
        Webcam.takeSound(duration);
    };

    $scope.removeFile = function (index) {
        var file = $scope.files[index];

        file.deleting = true;
        Webcam.remove(file.webPath).then(function () {
            $scope.files.splice(index, 1);
        });
    };
});
