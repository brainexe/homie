App.service('Widget.webcam', ['Webcam', function(Webcam) {
    return {
        render: function ($scope, widget) {
            $scope.takeShot = function () {
                Webcam.takeShot();
            };

            $scope.takeVideo = function () {
                var duration = prompt('Duration');
                Webcam.takeVideo(duration);
            };

            $scope.takeSound = function () {
                var duration = prompt('Duration');
                Webcam.takeSound(duration);
            };
        }
    }
}]);
