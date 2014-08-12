
App.ng.controller('EggTimerController', ['$scope', function($scope) {
	$scope.jobs = {};

	$.get('/egg_timer/', function(data) {
		$scope.jobs = data.jobs;
		$scope.$apply();
	});

	$scope.addTimer = function() {
		var payload = {
			time: $scope.time,
			text: $scope.text
		};

		$.post('/egg_timer/add/', payload, function(new_jobs) {
			$scope.jobs = new_jobs;
			$scope.$apply();
		});
	};

	/**
	 * @param {String} job_id
	 */
	$scope.deleteTimer = function(job_id) {
		job_id = job_id.split(':')[1];
		console.log(job_id);
		$.post('/egg_timer/delete/{0}/'.format(job_id), function(new_jobs) {
			console.log(new_jobs);
			$scope.jobs = new_jobs;
			$scope.$apply();
		});
	};
}]);
