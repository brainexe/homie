
App.controller('SensorValueDetailModalController', /*@ngInject*/ function($scope, $uibModalInstance, Sensor, value) {
    $scope.value = value;

    $scope.delete = function() {
        Sensor.deleteValue(value.series.sensorId, value.x).then((sensor) =>
            $uibModalInstance.close(sensor.data)
        );
    };

    $scope.close = $uibModalInstance.close;
});
