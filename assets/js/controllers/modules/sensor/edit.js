
App.controller('EditSensorsController', /*@ngInject*/ function($scope, $uibModalInstance, Sensor, SensorFormatter, SensorTags, lodash) {
    $scope.sensors = [];
    $scope.types   = {};
    $scope.tags    = {};
    $scope.orderBy = 'name';
    $scope.showDisabled = false;
    $scope.search = '';

    $scope.setOrderBy = function(key) {
        if ($scope.orderBy === key) {
            key = '-' + key;
        }

        $scope.orderBy = key;
    };

    function getSensorIndex(sensor) {
        return lodash.findIndex($scope.sensors, ['sensorId', sensor.sensorId]);
    }

    $scope.$on('sensor.update', function(event, sensorVo) {
        var index = getSensorIndex(sensorVo);
        $scope.sensors[index] = sensorVo;
    });

    Sensor.getAll().success(function(data) {
        $scope.sensors    = data.sensors;
        $scope.types      = data.types;
        $scope.formatters = data.formatters;

        $scope.tags = SensorTags.getTagsFromSensors(data.sensors);
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
            if (!sensor || sensor.edit) {
                return true;
            } else if (!$scope.showDisabled && sensor.interval <= 0) {
                return false;
            }

            var parts = [
                sensor.name,
                sensor.type,
                sensor.description,
                sensor.parameter
            ];
            parts.concat(sensor.tags);

            var text = parts.join(' ').toLowerCase();

            return text.includes(search);
        });
    };

    $scope.reload = sensorId => Sensor.forceReadValue(sensorId);
	$scope.close = () => $uibModalInstance.close();

    $scope.edit = function(sensor) {
        if (sensor.edit) {
            Sensor.edit(sensor).success(function(newSensor) {
                var index = getSensorIndex(sensor);
                $scope.sensors[index] = newSensor;
            });
        }

        sensor.edit = !sensor.edit;
    };
});
