
App.controller('SensorValueDetailModal', ['$scope', '$rootScope', '$uibModalInstance', 'Sensor', 'value', function($scope, $rootScope, $uibModalInstance, Sensor, value) {
    $scope.value = value;

    $scope.delete = function() {
        Sensor.deleteValue(value.series.sensorId, value.x).success(function(sensor) {
            $uibModalInstance.close(sensor);
        });
    };

    $scope.close = function() {
        $uibModalInstance.close();
    };
}]);
