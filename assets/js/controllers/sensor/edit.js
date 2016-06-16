
App.controller('EditSensorsController', ['$scope', '$uibModalInstance', 'Sensor', 'Sensor.Formatter', 'Sensor.Tags', function($scope, $uibModalInstance, Sensor, SensorFormatter, Tags) {
    $scope.sensors = [];
    $scope.types   = {};
    $scope.tags    = {};
    $scope.orderBy = 'name';
    $scope.showDisabled = false;
    $scope.search = '';

    $scope.setOrderBy = function(key) {
        if ($scope.orderBy == key) {
            key = '-' + key;
        }

        $scope.orderBy = key;
    };

    $scope.$on('sensor.update', function(event, sensorVo) {
        var index = getSensorIndex(sensorVo);
        $scope.sensors[index] = sensorVo;
    });

    // todo use $index
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

        $scope.tags = Tags.getTagsFromSensors(data.sensors);
    });

    $scope.formatValue = function(value, sensor) {
        var formatter = SensorFormatter.getFormatter(sensor.formatter);
        return formatter(value);
    };

	$scope.deleteSensor = function(sensor, $index) {
        return Sensor.deleteSensor(sensor.sensorId).success(function() {
            $scope.sensors.splice($index, 1);
        });
	};

    $scope.searchSensor = function (search) {
        search = search.toLowerCase();

        return $scope.sensors.filter(function(sensor) {
            if (!$scope.showDisabled && sensor.interval <= 0) {
                return false;
            } else if (!sensor) {
                return true;
            }

            var parts = [];
            parts.push(sensor.name);
            parts.push(sensor.description);
            parts.concat(sensor.tags);

            var text = parts.join(' ').toLowerCase();

            return text.indexOf(search) > -1;
        })
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
