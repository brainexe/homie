
App.controller('EggTimerController', /*@ngInject*/ function ($scope, EggTimer, Sound, MessageQueue) {
    const SOUND_FILE = '/sounds/egg_timer.mp3';

    $scope.time = '';
    $scope.text = '';
    $scope.jobs = {};


    MessageQueue.getJobs(EggTimer.JOB_ID).then(function (jobs) {
        $scope.jobs = jobs;
    });

    $scope.$on(MessageQueue.JOBS_HANDLED, function(event, data) {
        let job = data.job;
        if ($scope.jobs[job.jobId]) {
            delete $scope.jobs[job.jobId];
        }
    });

    $scope.addTimer = function () {
        EggTimer.setTimer($scope.time, $scope.text).then(function (job) {
            $scope.jobs[job.data.jobId] = job.data;
        });

        $scope.time = '';
        $scope.text = '';
    };

    /**
     * @param {String} jobId
     */
    $scope.removeOverdue = function (jobId) {
        Sound.play(SOUND_FILE);

        delete $scope.jobs[jobId];
    };

    /**
     * @param {String} jobId
     */
    $scope.deleteTimer = function (jobId) {
        MessageQueue.deleteJob(jobId).then(function() {
            $scope.removeOverdue(jobId);
        });
    };
});
