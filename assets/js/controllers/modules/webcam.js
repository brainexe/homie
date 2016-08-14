App.controller('WebcamController', /*@ngInject*/ function ($scope, Webcam, Flash, _) {
    $scope.files = [];

    function update() {
        Webcam.getData().success(function (data) {
            $scope.files = data.files;
        });
    }
    update();

    $scope.$on('webcam.took_photo', update);
    $scope.$on('webcam.took_video', update);
    $scope.$on('webcam.took_sound', update);

    $scope.takeShot = function () {
        Webcam.takeShot().success(function() {
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
        var shot = $scope.files[index];

        shot.deleting = true;
        Webcam.remove(shot.webPath).success(function () {
            $scope.files.splice(index, 1);
        });
    };
});
