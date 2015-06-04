
App.controller('AdminSensorsController', ['$scope', '$modalInstance', 'Sensor', function($scope, $modalInstance, Sensor) {
    $scope.sensors = [];
    $scope.types   = {};

    Sensor.getAll().success(function(data) {
        $scope.sensors = data.sensors;
        $scope.types   = data.types;
    });

	$scope.deleteSensor = function(sensor) {
        return Sensor.deleteSensor(sensor.sensorId).success(function() {
            var index = $scope.sensors.indexOf(sensor);
            $scope.sensors.splice(index, 1);
        });
	};

	$scope.close = function() {
		$modalInstance.close();
	}
}]);
