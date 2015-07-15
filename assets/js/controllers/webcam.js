App.controller('WebcamController', ['$scope', 'Webcam', function ($scope, Webcam) {
    $scope.files = [];

    function update() {
        Webcam.getData().success(function (data) {
            $scope.files = data.files;
        });
    }
    update();

    $scope.takeShot = function () {
        Webcam.takeShot().success(function() {
            $scope.$broadcast('flash', ['Cheese...', 'info']);
        });
    };

    $scope.takeVideo = function () {
        var duration = prompt('Duration');
        Webcam.takeVideo(duration);
    };

    $scope.takeSound = function () {
        var duration = prompt('Duration');
        Webcam.takeSound(duration);
    };

    $scope.removeFile = function (index) {
        var shot = $scope.files[index];

        shot.deleting = true;
        Webcam.remove(shot.webPath).success(function () {
            $scope.files.slice(index, 1);
        });
    };

    $scope.$on('webcam.took_photo', update);
    $scope.$on('webcam.took_video', update);
    $scope.$on('webcam.took_sound', update);
}]);
