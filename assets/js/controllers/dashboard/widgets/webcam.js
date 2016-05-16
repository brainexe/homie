App.service('Widget.webcam', ['Webcam', '_', function(Webcam, _) {
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
                var duration = prompt(_('Duration'));
                Webcam.takeVideo(duration);
            };

            $scope.takeSound = function () {
                var duration = prompt(_('Duration'));
                Webcam.takeSound(duration);
            };
        }
    }
}]);
