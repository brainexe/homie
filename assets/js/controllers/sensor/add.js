
App.controller('AddSensorsController', /*@ngInject*/ function($scope, $uibModalInstance, Sensor, Nodes, SensorTags) {
    $scope.sensors    = [];
    $scope.parameters = false;
    $scope.nodes      = {};
    $scope.tags       = [];
    $scope.newSensor  = {
        interval: 5,
        tags: [],
        color: randomHexColor()
    };

    function randomHexColor() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16);
    }

    Sensor.getAll().success(function(data) {
        $scope.sensors    = data.sensors;
        $scope.types      = data.types;
        $scope.formatters = data.formatters;

        $scope.tags = SensorTags.getTagsFromSensors(data.sensors);
    });

    Nodes.getData().success(function(data) {
        $scope.nodes = data.nodes;
    });

    $scope.changedType = function(sensorType) {
        Sensor.parameters(sensorType).success(function(parameters) {
            $scope.parameters = parameters || [];
        });
    };

    $scope.add = function(newSensor) {
        Sensor.add(newSensor).success(function(sensor) {
            $uibModalInstance.close(sensor);
        });
    };

    $scope.close = $uibModalInstance.close;
});
