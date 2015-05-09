App.Radios = {
	_radios: null,

	loadAll: function() {
		var self = this;
		return new Promise(function(resolve, reject) {
			if (self._radios !== null) {
				resolve(self._radios);
				return;
			}

			$.get('/radio/', function (data) {
				resolve(data.radios);
				self._radios = data;
			});
		});
	},

    setRadio: function (radioId, status) {
        return $.post('/radio/status/{0}/{1}/'.format(radioId, status));
    }
};

App.ng.controller('RadioController', ['$scope', function ($scope) {
	$scope.radios     = {};
	$scope.radio_jobs = {};
	$scope.pins       = {};
	$scope.new_job    = {};
	$scope.editMode   = false;

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
        App.Radios.setRadio(radio.radioId, status).then(function() {
            radio.status = status;
            $scope.$apply();
        });
	};

	/**
	 * @param {Number} radioId
	 */
	$scope.deleteRadio = function (radioId) {
		if (!confirm(gettext('Remove this Radio-Job?'))) {
			return;
		}

		$.post('/radio/delete/{0}/'.format(radioId), function () {
			delete $scope.radios[radioId];
			$scope.$apply();
		});
	};

	$scope.highlight = function(radio) {
		$scope.new_job.radioId = radio.radioId;
        document.getElementById('new_radio_job_time').focus();
	};

	$scope.addRadio = function(newRadio) {
		$.post('/radio/add/', newRadio, function(data) {
            $scope.radios[data.radioId] = data;
			$scope.$apply();
		});
	};

	$scope.new_radio = {};
	$scope.addRadioJob = function(newJob) {
		$.post('/radio/job/add/', newJob, function(data) {
			$scope.radio_jobs = data;
			$scope.job_time   = '';
			$scope.$apply();
		});
	};

	$scope.deleteRadioJob = function(jobId) {
		var eventId = jobId.split(':')[1];
		$.post('/radio/job/delete/{0}/'.format(eventId), function() {
			delete $scope.radio_jobs[jobId];
			$scope.$apply();
		});
	}
}]);
