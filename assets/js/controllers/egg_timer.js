
App.ng.controller('EggTimerController', ['$scope', 'EggTimer', function ($scope, EggTimer) {
    $scope.jobs = {};

    EggTimer.getJobs().success(function (data) {
        $scope.jobs = data.jobs;
    });

    $scope.addTimer = function () {
        EggTimer.setTimer($scope.time, $scope.text).success(function (newJobs) {
            $scope.jobs = newJobs;
        });

        $scope.time = '';
        $scope.text = '';
    };

    /**
     * @param {String} jobId
     */
    $scope.deleteTimer = function (jobId) {
        jobId = jobId.split(':')[1];

        EggTimer.delete(jobId).success(function (newJobs) {
            $scope.jobs = newJobs;
        });
    };
}]);
