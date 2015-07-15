
App.controller('EggTimerController', ['$scope', 'EggTimer', 'MessageQueue', function ($scope, EggTimer, MessageQueue) {
    $scope.jobs = {};

    MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function (data) {
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
        MessageQueue.deleteJob(jobId).then(function() {
            MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                $scope.jobs = data;
            });
        });
    };
}]);
