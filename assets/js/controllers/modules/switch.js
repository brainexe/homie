
App.controller('SwitchController', /*@ngInject*/ function ($scope, Switches, Nodes, MessageQueue, _) {
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

    Switches.getData().then(function (data) {
        $scope.switches  = data.data.switches;
        $scope.radioPins = data.data.radioPins;
    });

    Nodes.getData().then(function(data) {
        $scope.nodes = data.data.nodes;
    });

    MessageQueue.getJobs(Switches.JOB_ID).then(function(jobs) {
        $scope.jobs = jobs;
    });

    $scope.$on(MessageQueue.JOBS_HANDLED , function(event, data) {
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
        Switches.setStatus(switchVO.switchId, status).then(function () {
            switchVO.status = status;
        });
    };

    /**
     * @param {Number} switchId
     */
    $scope.delete = function (switchId) {
        Switches.delete(switchId).then(function () {
            delete $scope.switches[switchId];
        });
    };

    $scope.highlight = function (switchVO) {
        $scope.newJob.switchId = switchVO.switchId;
        document.getElementById('newSwitchJobTime').focus();
    };

    $scope.addSwitch = function (newSwitch) {
        Switches.add(newSwitch).then(function (data) {
            $scope.switches[data.switchId] = data.data;
        });
    };

    $scope.addJob = function (newJob) {
        Switches.addJob(newJob).then(function() {
            MessageQueue.getJobs(Switches.JOB_ID, true).then(function(jobs) {
                $scope.jobs = jobs;
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
});
