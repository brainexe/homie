
App.controller('EspeakController', ['$scope', 'Speak', 'MessageQueue', function ($scope, Speak, MessageQueue) {
    $scope.jobs     = {};
    $scope.speakers = {};
    $scope.pending  = true;

    Speak.getData().success(function (data) {
        $scope.pending  = false;
        $scope.jobs     = data.jobs;
        $scope.speakers = data.speakers;
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

        Speak.speak(payload).success(function (newJobs) {
            $scope.pending = false;
            $scope.jobs    = newJobs;
        });
    }
}]);
