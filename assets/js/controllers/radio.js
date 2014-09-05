App.ng.controller('RadioController', ['$scope', function ($scope) {
	$scope.radios = {};
	$scope.radio_jobs = {};
	$scope.pins = {};

	$.get('/radio/', function(data) {
		$scope.radios = data.radios;
		$scope.radio_jobs = data.radio_jobs;
		$scope.pins = data.pins;
		$scope.$apply();
	});

	/**
	 * @param {Object} radio
	 * @param {Number} status
	 */
	$scope.setStatus = function (radio, status) {
		$.post('/radio/status/{0}/{1}/'.format(radio.id, status));
	};

	/**
	 * @param {Number} radio_id
	 */
	$scope.deleteRadio = function (radio_id) {
		if (!confirm('Remove this Radio-Job?')) {
			return;
		}

		$.post('/radio/delete/{0}/'.format(radio_id), function () {
			delete $scope.radios[radio_id];
			$scope.$apply();
		});
	};

	$scope.saveRadio = function(radio) {
		//todo

		radio.edit = false;
	};

	$scope.addRadio = function() {
		var payload = {
			name: $scope.name,
			description: $scope.description,
			code: $scope.code,
			pin: $scope.pin
		};

		$.post('/radio/add/', payload, function(data) {
			$scope.radios[data.id] = data;
			$scope.pin = $scope.description = $scope.name = $scope.code = '';
			$scope.$apply();
		});
	};

	$scope.new_radio = {};
	$scope.addRadioJob = function(new_job) {
		$.post('/radio/job/add/', new_job, function (data) {
			$scope.radio_jobs = data;
			$scope.job_time = '';
			$scope.$apply();
		});
	};

	$scope.deleteRadioJob = function(job_id) {
		var event_id = job_id.split(':')[1];
		$.post('/radio/job/delete/{0}/'.format(event_id), function(){
			delete $scope.radio_jobs[job_id];
			$scope.$apply();
		});
	}
}]);
