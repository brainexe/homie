App.ng.controller('StatusController', ['$scope', function($scope) {
	$scope.textarea = '';
	$scope.stats    = {};
	$scope.jobs     = {};

    $scope.update = function() {
        $.get('/status/', function(data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.$apply();
        });
    };

    $scope.update();

    setInterval(function() {
        $scope.update();
    }, 2500);

	$scope.startUpdate = function() {
		if (!confirm('Do you want to start self update? This may take some time...')) {
			return false;
		}

		$.post('/status/self_update/');
	};

	/**
	 * @param {String} event_id
	 */
	$scope.deleteEvent = function(event_id) {
		$.post('/status/event/delete/', {job_id:event_id}, function() {
			delete $scope.jobs[event_id];
			$scope.$apply();
		});
	};

	App.Layout.$scope.$on('update.process', function(event) {
		$scope.textarea += event.payload;
	});

	App.Layout.$scope.$on('update.done', function(event) {
		$scope.textarea += "Update is done!";
	});
}]);
