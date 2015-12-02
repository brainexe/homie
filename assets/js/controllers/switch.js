
App.controller('SwitchController', ['$scope', 'Radios', 'MessageQueue', '_', function ($scope, Radios, MessageQueue, _) {
    $scope.radios    = {};
    $scope.radioJobs = {};
    $scope.pins      = {};
    $scope.newJob    = {};
    $scope.editMode  = false;

    Radios.getData().success(function (data) {
        $scope.radios = data.radios;
        $scope.pins   = data.pins;
    });

    MessageQueue.getJobs(Radios.JOB_ID, true).success(function(data) {
        $scope.radioJobs = data;
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
        Radios.addJob(newJob).success(function() {
            MessageQueue.getJobs(Radios.JOB_ID, true).success(function(data) {
                $scope.radioJobs = data;
            });
            $scope.job_time = '';
        });
    };

    $scope.deleteRadioJob = function (jobId) {
        MessageQueue.deleteJob(jobId).then(function() {
            delete $scope.radioJobs[jobId];
        });
    }
}]);
