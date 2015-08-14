App.service('Widget.webcam', ['Webcam', function(Webcam) {
    return {
        render: function ($scope, widget) {

            $scope.loadRecentImage = function() {
                Webcam.getRecent().success(function(data) {
                    $scope.recent = data;
                });
            };

            if (widget.showImage) {
                $scope.loadRecentImage();
            }

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
