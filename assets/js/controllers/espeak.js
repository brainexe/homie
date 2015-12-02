
App.controller('EspeakController', ['$scope', 'Speak', 'MessageQueue', function ($scope, Speak, MessageQueue) {
    $scope.jobs     = {};
    $scope.speakers = {};
    $scope.pending  = false;

    Speak.getSpeakers().success(function (data) {
        $scope.speakers = data.speakers;
    });

    MessageQueue.getJobs(Speak.JOB_ID, true).success(function(data) {
        $scope.jobs = data;
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

        Speak.speak(payload).success(function() {
            $scope.pending = false;
            MessageQueue.getJobs(Speak.JOB_ID, true).success(function(data) {
                $scope.jobs = data;
            });
        });
    }
}]);
