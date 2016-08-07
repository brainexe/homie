
App.controller('SensorValueDetailModal', /*@ngInject*/ function($scope, $rootScope, $uibModalInstance, Sensor, value) {
    $scope.value = value;

    $scope.delete = function() {
        Sensor.deleteValue(value.series.sensorId, value.x).success(function(sensor) {
            $uibModalInstance.close(sensor);
        });
    };

    $scope.close = $uibModalInstance.close;
});
