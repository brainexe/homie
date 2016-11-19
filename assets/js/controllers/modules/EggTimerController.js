
App.controller('EggTimerController', /*@ngInject*/ function ($scope, EggTimer, MessageQueue) {
    $scope.time = '';
    $scope.text = '';
    $scope.jobs = {};

    MessageQueue.getJobs(EggTimer.JOB_ID).success(function (jobs) {
        $scope.jobs = jobs;
    });

    $scope.$on(MessageQueue.JOBS_HANDLED, function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            delete $scope.jobs[job.jobId];
        }
    });

    $scope.addTimer = function () {
        EggTimer.setTimer($scope.time, $scope.text).success(function (job) {
            $scope.jobs[job.jobId] = job;
        });

        $scope.time = '';
        $scope.text = '';
    };

    /**
     * @param {String} jobId
     */
    $scope.deleteTimer = function (jobId) {
        MessageQueue.deleteJob(jobId).then(function() {
            delete $scope.jobs[jobId];
        });
    };
});
