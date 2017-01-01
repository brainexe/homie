
App.directive('jobProgress', /*@ngInject*/ function () {
    return {
        restrict: 'E',
        templateUrl: '/templates/directives/jobProgress.html',
        link ($scope, elem) {
            var style = elem[0].querySelector('.progress-bar').style;
            $scope.$on('secondTimer', function (event, now) {
                var progress = (now / 1000 - $scope.job.startTime) / ($scope.job.timestamp - $scope.job.startTime);
                $scope.progress = Math.round(progress * 100 * 1000) / 1000;

                if ($scope.progress <= 0) {
                    $scope.progress = 0;
                    $scope.class = 'progress-bar-success';
                } else if ($scope.progress >= 100) {
                    $scope.progress = 100;
                    $scope.class = 'progress-bar-danger';
                } else {
                    $scope.class = 'progress-bar-success';
                }
                style.width = $scope.progress + '%';
            });
        },
        scope: {
            job:     "=",
            overdue: "=",
        }
    };
});
