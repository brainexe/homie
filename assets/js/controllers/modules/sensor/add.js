
App.controller('AddSensorsController', /*@ngInject*/ function($scope, $uibModalInstance, Sensor, Nodes, SensorTags) {
    $scope.sensors    = [];
    $scope.parameters = false;
    $scope.nodes      = {};
    $scope.tags       = [];

    function randomHexColor() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16);
    }

    $scope.newSensor  = {
        interval: 5,
        tags: [],
        color: randomHexColor()
    };

    Sensor.getAll().then(function(data) {
        data = data.data;
        $scope.sensors    = data.sensors;
        $scope.types      = data.types;
        $scope.formatters = data.formatters;

        $scope.tags = SensorTags.getTagsFromSensors(data.sensors);
    });

    Nodes.getData().then(function(data) {
        $scope.nodes = data.data.nodes;
    });

    $scope.changedType = function(sensorType) {
        Sensor.parameters(sensorType).then(function(parameters) {
            $scope.parameters = parameters.data || [];
        });
    };

    $scope.add = function(newSensor) {
        Sensor.add(newSensor).then(function(sensor) {
            $uibModalInstance.close(sensor.data);
        });
    };

    $scope.close = $uibModalInstance.close;
});
