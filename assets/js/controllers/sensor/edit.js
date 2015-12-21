
App.controller('EditSensorsController', ['$scope', '$rootScope', '$uibModalInstance', 'Sensor', 'Sensor.Formatter', function($scope, $rootScope, $uibModalInstance, Sensor, SensorFormatter) {
    $scope.sensors = [];
    $scope.types   = {};

    $rootScope.$on('sensor.update', function(event, sensorVo) {
        var index = getSensorIndex(sensorVo);
        $scope.sensors[index] = sensorVo;
    });

    function getSensorIndex(sensor) {
        var index = $scope.sensors.indexOf(sensor);
        if (index != -1) {
            return index;
        }

        for (var i in $scope.sensors) {
            if ($scope.sensors[i].sensorId == sensor.sensorId) {
                return i;
            }
        }
    }

    Sensor.getAll().success(function(data) {
        $scope.sensors    = data.sensors;
        $scope.types      = data.types;
        $scope.formatters = data.formatters;
    });

    // todo put into own |filter
    $scope.formatValue = function(value, sensor) {
        var formatter = SensorFormatter.getFormatter(sensor.formatter);
        return formatter(value);
    };

	$scope.deleteSensor = function(sensor) {
        return Sensor.deleteSensor(sensor.sensorId).success(function() {
            var index = getSensorIndex(sensor);
            $scope.sensors.splice(index, 1);
        });
	};

    $scope.reload = function(sensorId) {
        Sensor.forceReadValue(sensorId);
    };

	$scope.close = function() {
		$uibModalInstance.close();
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
