
App.controller('SwitchController', ['$scope', 'Switches', 'Nodes', 'MessageQueue', '_', function ($scope, Switches, Nodes, MessageQueue, _) {
    $scope.switches  = {};
    $scope.jobs      = {};
    $scope.radioPins = {};
    $scope.newJob    = {};
    $scope.editMode  = false;
    $scope.newSwitch = {};
    $scope.nodes     = {};
    $scope.types     = {
        'radio':    {name: _('443 MHz Radio'), template: '/templates/switch/addForm/radio.html'},
        'gpio':     {name: _('RaspberryPi Pin'), template: '/templates/switch/addForm/raspberry.html'},
        'arduino':  {name: _('Arduino Pin'), template: '/templates/switch/addForm/arduino.html'},
        'particle': {name: _('Particle'), template: '/templates/switch/addForm/particle.html'}
    };

    Switches.getData().success(function (data) {
        $scope.switches  = data.switches;
        $scope.radioPins = data.radioPins;
    });

    Nodes.getData().success(function(data) {
        $scope.nodes = data.nodes;
    });

    MessageQueue.getJobs(Switches.JOB_ID).success(function(data) {
        $scope.jobs = data;
    });

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            delete $scope.jobs[job.jobId];
        }
    });

    /**
     * @param {Object} switchVO
     * @param {Number} status
     */
    $scope.setStatus = function (switchVO, status) {
        Switches.setStatus(switchVO.switchId, status).success(function () {
            switchVO.status = status;
        });
    };

    /**
     * @param {Number} switchId
     */
    $scope.delete = function (switchId) {
        if (!confirm(_('Remove this Job?'))) {
            return;
        }

        Switches.delete(switchId).success(function () {
            delete $scope.switches[switchId];
        });
    };

    $scope.highlight = function (switchVO) {
        $scope.newJob.switchId = switchVO.switchId;
        document.getElementById('newSwitchJobTime').focus();
    };

    $scope.addSwitch = function (newSwitch) {
        Switches.add(newSwitch).success(function (data) {
            $scope.switches[data.switchId] = data;
        });
    };

    $scope.addJob = function (newJob) {
        Switches.addJob(newJob).success(function() {
            MessageQueue.getJobs(Switches.JOB_ID, true).success(function(data) {
                $scope.jobs = data;
            });
        });
    };

    $scope.deleteJob = function (jobId) {
        MessageQueue.deleteJob(jobId).then(function() {
            delete $scope.jobs[jobId];
        });
    };

    $scope.save = function () {
        // TODO implement save function
    };
}]);
