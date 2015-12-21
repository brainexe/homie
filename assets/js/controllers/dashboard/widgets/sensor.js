
App.service('Widget.sensor', ['Sensor', '$rootScope', 'Sensor.Formatter', 'Sensor.Stats', function(Sensor, $rootScope, SensorFormatter, SensorStats) {
    return {
        render: function ($scope, widget) {
            $scope.updating = false;
            $scope.format   = SensorFormatter.getFormatter('noop');

            function update() {
                Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
                    $scope.format = SensorFormatter.getFormatter(sensorData.formatter);

                    $scope.setTitle("{0}".format(sensorData.name));

                    $scope.sensor = sensorData;
                    $scope.value  = sensorData.lastValue;
                });

                Sensor.getValues(widget.sensor_id, '?from={0}'.format(~~widget.from)).success(function (data) {
                    if (!data.json) {
                        return;
                    }
                    $scope.stats = SensorStats.getStats(data.json[0]);
                });
            }

            update();

            $rootScope.$on('sensor.update', function() {
                $scope.updating = false;
                update();
            });

            $scope.reload = function() {
                $scope.updating = true;
                Sensor.forceReadValue(widget.sensor_id);
            };
        }
    };
}]);

