App.Radios = {
	_radios: null,

	loadAll: function() {
		var self = this;
		return new Promise(function(resolve, reject) {
			if (self._radios !== null) {
				resolve(self._radios);
				return;
			}

			$.get('/radios/', function (data) {
				resolve(data.radios);
				self._radios = data;
			});
		});
	},

    setRadio: function (radioId, status) {
        return $.post('/radios/{0}/status/{1}/'.format(radioId, status));
    }
};

App.ng.controller('RadioController', ['$scope', function ($scope) {
	$scope.radios     = {};
	$scope.radioJobs  = {};
	$scope.pins       = {};
	$scope.newJob    = {};
	$scope.editMode   = false;

	$.get('/radios/', function(data) {
		$scope.radios = data.radios;
		$scope.radioJobs = data.radioJobs;
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

		$.ajax_delete('/radios/{0}/'.format(radioId), function () {
			delete $scope.radios[radioId];
			$scope.$apply();
		});
	};

	$scope.highlight = function(radio) {
		$scope.newJob.radioId = radio.radioId;
        document.getElementById('new_radioJob_time').focus();
	};

	$scope.addRadio = function(newRadio) {
		$.post('/radios/', newRadio, function(data) {
            $scope.radios[data.radioId] = data;
			$scope.$apply();
		});
	};

	$scope.newRadio = {};
	$scope.addRadioJob = function(newJob) {
		$.post('/radios/jobs/', newJob, function(data) {
			$scope.radioJobs = data;
			$scope.job_time   = '';
			$scope.$apply();
		});
	};

	$scope.deleteRadioJob = function(jobId) {
		var eventId = jobId.split(':')[1];
		$.ajax_delete('/radios/jobs/{0}/'.format(eventId), function() {
			delete $scope.radioJobs[jobId];
			$scope.$apply();
		});
	}
}]);
