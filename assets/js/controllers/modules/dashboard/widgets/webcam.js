App.service('Widget.webcam', /*@ngInject*/ function(Webcam, Prompt, _) {
    return {
        template: '/templates/widgets/webcam.html',
        render ($scope, widget) {
            function loadRecentImage() {
                Webcam.getRecent().then(function(data) {
                    $scope.recent = data.data;
                });
            }

            $scope.$on('webcam.took_photo', loadRecentImage);

            if (widget.showImage) {
                loadRecentImage();
            }

            $scope.takeShot = Webcam.takeShot;

            $scope.takeVideo = function () {
                Prompt(_('Duration')).then(function (duration) {
                    Webcam.takeVideo(duration);
                });
            };

            $scope.takeSound = function () {
                Prompt(_('Duration')).then(function (duration) {
                    Webcam.takeSound(duration);
                });
            };
        }
    };
});
