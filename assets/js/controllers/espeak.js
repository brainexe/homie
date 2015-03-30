
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
	 * @param {String} event_id
	 */
	$scope.deleteEvent = function(event_id) {
		var job_id = event_id.split(':')[1];
		$.post('/espeak/job/delete/', {job_id:job_id}, function() {
			delete $scope.jobs[event_id];
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

        App.Speak.speak(payload).then(function(new_jobs) {
            $scope.pending  = false;
            $scope.jobs = new_jobs;
            $scope.$apply();
        });
	}
}]);
