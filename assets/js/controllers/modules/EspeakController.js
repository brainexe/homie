
App.controller('EspeakController', /*@ngInject*/ function ($scope, Speak, MessageQueue) {
    $scope.jobs     = {};
    $scope.speakers = {};
    $scope.pending  = false;

    Speak.getSpeakers().then(function (speakers) {
        $scope.speakers = speakers.data;
    });

    MessageQueue.getJobs(Speak.JOB_ID).then(function(jobs) {
        $scope.jobs = jobs;
    });

    $scope.$on(MessageQueue.JOBS_HANDLED, function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            delete $scope.jobs[job.jobId];
        }
    });

    /**
     * @param {String} eventId
     */
    $scope.deleteEvent = function (eventId) {
        MessageQueue.deleteJob(eventId).then(function() {
            delete $scope.jobs[eventId];
        });
    };

    $scope.addEspeak = function () {
        $scope.pending = true;
        var payload = {
            text:    $scope.text,
            delay:   $scope.delay,
            volume:  $scope.volume,
            speed:   $scope.speed,
            speaker: $scope.speaker
        };

        Speak.speak(payload).then(function(result) {
            let job = result.data;

            $scope.pending = false;
            $scope.jobs[job.jobId] = job;
        });
    };
});
