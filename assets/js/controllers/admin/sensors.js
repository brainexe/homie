
App.controller('AdminSensorsController', ['$scope', '$modalInstance', 'Sensor', function($scope, $modalInstance, Sensor) {
    $scope.sensors = [];
    $scope.types   = {};

    function getSensorIndex(sensor) {
        return $scope.sensors.indexOf(sensor);
    }

    Sensor.getAll().success(function(data) {
        $scope.sensors = data.sensors;
        $scope.types   = data.types;
    });

	$scope.deleteSensor = function(sensor) {
        return Sensor.deleteSensor(sensor.sensorId).success(function() {
            var index = getSensorIndex(sensor);
            $scope.sensors.splice(index, 1);
        });
	};

	$scope.close = function() {
		$modalInstance.close();
	};

    $scope.edit = function(sensor) {
        if (sensor.edit) {
            Sensor.edit(sensor).success(function(newSensor) {
                var index = getSensorIndex(sensor);
                $scope.sensors[index] = newSensor;
            });
        }

        sensor.edit = !sensor.edit
    };
}]);
