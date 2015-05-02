
App.EggTimer = {
    setTimer: function(time, text) {
        var payload = {
            time: time,
            text: text
        };

        return $.post('/egg_timer/add/', payload);
    }
};

App.ng.controller('EggTimerController', ['$scope', function($scope) {
	$scope.jobs = {};

	$.get('/egg_timer/', function(data) {
		$scope.jobs = data.jobs;
		$scope.$apply();
	});

	$scope.addTimer = function() {
        App.EggTimer.setTimer($scope.time, $scope.text).then(function(new_jobs) {
            $scope.jobs = new_jobs;
            $scope.$apply();
        });

		$scope.time = '';
		$scope.text = '';
	};

	/**
	 * @param {String} job_id
	 */
	$scope.deleteTimer = function(job_id) {
		job_id = job_id.split(':')[1];
		$.post('/egg_timer/delete/{0}/'.format(job_id), function(new_jobs) {
			$scope.jobs = new_jobs;
			$scope.$apply();
		});
	};
}]);
