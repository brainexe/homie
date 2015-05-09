
App.Speak = {
    speak: function(payload) {
        return $.post('/espeak/speak/', payload);
    }
};

App.ng.controller('EspeakController', ['$scope', function($scope) {
	$scope.jobs     = {};
	$scope.speakers = {};
    $scope.pending  = false;

	$.get('/espeak/', function(data) {
		$scope.jobs     = data.jobs;
		$scope.speakers = data.speakers;
		$scope.$apply();
	});

	/**
	 * @param {String} eventId
	 */
	$scope.deleteEvent = function(eventId) {
		var jobId = eventId.split(':')[1];
		$.post('/espeak/job/delete/', {job_id:jobId}, function() {
			delete $scope.jobs[eventId];
			$scope.$apply();
		});
	};

	$scope.addEspeak = function() {
        $scope.pending  = true;
		var payload = {
			text:    $scope.text,
			delay:   $scope.delay,
			volume:  $scope.volume,
			speed:   $scope.speed,
			speaker: $scope.speaker
		};

        App.Speak.speak(payload).then(function(newJobs) {
            $scope.pending  = false;
            $scope.jobs     = newJobs;
            $scope.$apply();
        });
	}
}]);
