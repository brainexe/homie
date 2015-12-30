
App.controller('EggTimerController', ['$scope', 'EggTimer', 'MessageQueue', function ($scope, EggTimer, MessageQueue) {
    $scope.jobs = {};

    MessageQueue.getJobs(EggTimer.JOB_ID).success(function (data) {
        $scope.jobs = data;
    });

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            delete $scope.jobs[job.jobId];
        }
    });

    $scope.addTimer = function () {
        EggTimer.setTimer($scope.time, $scope.text).success(function () {
            MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                $scope.jobs = data;
            });
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
