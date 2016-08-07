App.service('Widget.webcam', /*@ngInject*/ function(Webcam, _) {
    return {
        template: '/templates/widgets/webcam.html',
        render: function ($scope, widget) {
            $scope.loadRecentImage = function() {
                Webcam.getRecent().success(function(data) {
                    $scope.recent = data;
                });
            };

            if (widget.showImage) {
                $scope.loadRecentImage();
            }

            $scope.takeShot = Webcam.takeShot;

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
});
