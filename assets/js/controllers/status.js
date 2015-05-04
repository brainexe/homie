App.ng.controller('StatusController', ['$scope', function($scope) {
	$scope.stats    = {};
	$scope.jobs     = {};

    $scope.update = function() {
        $.get('/stats/', function(data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.$apply();
        });
    };

    $scope.update();

    setInterval(function() {
        $scope.update();
    }, 1000);

	$scope.resetStats = function(key) {
		var url = '/stats/reset/'.format(key);
		$.post(url, {key:key}).then(function() {
			$scope.stats[key] = 0;
			$scope.$apply();
		});
	};

	/**
	 * @param {String} event_id
	 */
	$scope.deleteEvent = function(event_id) {
		$.post('/stats/event/delete/', {job_id:event_id}, function() {
			delete $scope.jobs[event_id];
			$scope.$apply();
		});
	};
}]);
