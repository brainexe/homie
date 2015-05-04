App.ng.controller('StatusController', ['$scope', function($scope) {
	$scope.textarea = '';
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

	$scope.startUpdate = function() {
		if (!confirm('Do you want to start self update? This may take some time...')) {
			return false;
		}

		$.post('/stats/self_update/');
	};

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

	App.Layout.$scope.$on('update.process', function(event) {
		$scope.textarea += event.payload;
	});

	App.Layout.$scope.$on('update.done', function(event) {
		$scope.textarea += "Update is done!";
	});
}]);
