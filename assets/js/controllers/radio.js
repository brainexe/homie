App.Radios = {
	_radios: null,

	loadAll: function() {
		var self = this;
		return new Promise(function(resolve, reject) {
			if (self._radios !== null) {
				resolve(self._radios);
				return;
			}

			$http.get('/radios/').success(function (data) {
				resolve(data.radios);
				self._radios = data;
			});
		});
	},

    setRadio: function (radioId, status) {
        return $.post('/radios/{0}/status/{1}/'.format(radioId, status));
    }
};

App.ng.controller('RadioController', ['$scope', '$http', function ($scope, $http) {
	$scope.radios     = {};
	$scope.radioJobs  = {};
	$scope.pins       = {};
	$scope.newJob    = {};
	$scope.editMode   = false;

	$http.get('/radios/').success(function(data) {
		$scope.radios = data.radios;
		$scope.radioJobs = data.radioJobs;
		$scope.pins = data.pins;
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

		$http.delete('/radios/{0}/'.format(radioId)).success(function () {
			delete $scope.radios[radioId];
		});
	};

	$scope.highlight = function(radio) {
		$scope.newJob.radioId = radio.radioId;
        document.getElementById('new_radioJob_time').focus();
	};

	$scope.addRadio = function(newRadio) {
		$http.post('/radios/', newRadio).success(function(data) {
            $scope.radios[data.radioId] = data;
		});
	};

	$scope.newRadio = {};
	$scope.addRadioJob = function(newJob) {
		$http.post('/radios/jobs/', newJob).success(function(data) {
			$scope.radioJobs = data;
			$scope.job_time  = '';
		});
	};

	$scope.deleteRadioJob = function(jobId) {
		var eventId = jobId.split(':')[1];
		$http.delete('/radios/jobs/{0}/'.format(eventId)).success(function() {
			delete $scope.radioJobs[jobId];
		});
	}
}]);
