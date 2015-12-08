
App.controller('AddSensorsController', ['$scope', '$rootScope', '$uibModalInstance', 'Sensor', 'Nodes', function($scope, $rootScope, $uibModalInstance, Sensor, Nodes) {
    $scope.sensors   = [];
    $scope.nodes     = {};
    $scope.newSensor = {
        interval: 5
    };

    Sensor.getAll().success(function(data) {
        $scope.sensors    = data.sensors;
        $scope.types      = data.types;
        $scope.formatters = data.formatters;
    });

    Nodes.getData().success(function(data) {
        $scope.nodes = data.nodes;
    });

    $scope.add = function(newSensor) {
        Sensor.add(newSensor).success(function(sensor) {
            $uibModalInstance.close(sensor);
        });
    };

    $scope.close = function() {
        $uibModalInstance.close();
    };
}]);
