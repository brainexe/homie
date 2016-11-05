
App.controller('SensorValueDetailModalController', /*@ngInject*/ function($scope, $uibModalInstance, Sensor, value) {
    $scope.value = value;

    $scope.delete = function() {
        Sensor.deleteValue(value.series.sensorId, value.x).success((sensor) =>
            $uibModalInstance.close(sensor)
        );
    };

    $scope.close = $uibModalInstance.close;
});
