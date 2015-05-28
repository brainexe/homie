
App.ng.controller('RadioController', ['$scope', 'Radios', '_', function ($scope, Radios, _) {
    $scope.radios    = {};
    $scope.radioJobs = {};
    $scope.pins      = {};
    $scope.newJob    = {};
    $scope.editMode  = false;

    Radios.getData().success(function (data) {
        $scope.radios    = data.radios;
        $scope.radioJobs = data.radioJobs;
        $scope.pins      = data.pins;
    });

    /**
     * @param {Object} radio
     * @param {Number} status
     */
    $scope.setStatus = function (radio, status) {
        Radios.setRadio(radio.radioId, status).success(function () {
            radio.status = status;
        });
    };

    /**
     * @param {Number} radioId
     */
    $scope.deleteRadio = function (radioId) {
        if (!confirm(_('Remove this Radio-Job?'))) {
            return;
        }

        Radios.deleteRadio(radioId).success(function () {
            delete $scope.radios[radioId];
        });
    };

    $scope.highlight = function (radio) {
        $scope.newJob.radioId = radio.radioId;
        document.getElementById('newRadioJobTime').focus();
    };

    $scope.addRadio = function (newRadio) {
        Radios.add(newRadio).success(function (data) {
            $scope.radios[data.radioId] = data;
        });
    };

    $scope.newRadio = {};
    $scope.addRadioJob = function (newJob) {
        Radios.addJob(newJob).success(function (data) {
            $scope.radioJobs = data;
            $scope.job_time = '';
        });
    };

    $scope.deleteRadioJob = function (jobId) {
        var eventId = jobId.split(':')[1];
        Radios.deleteJob(eventId).success(function () {
            delete $scope.radioJobs[jobId];
        });
    }
}]);
