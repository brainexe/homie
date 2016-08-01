
App.service('Widget.sensor', ['Sensor', 'Sensor.Formatter', 'Sensor.Stats', function(Sensor, SensorFormatter, SensorStats) {
    return {
        template: '/templates/widgets/sensor.html',
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
                    if (!data.json || !data.json[widget.sensor_id]) {
                        return;
                    }
                    $scope.stats = SensorStats.getStats(data.json[widget.sensor_id]);
                });
            }

            update();

            $scope.$on('sensor.update', function(event, data) {
                if (data.sensorId == widget.sensor_id) {
                    $scope.updating = false;
                    update();
                }
            });

            $scope.reload = function() {
                $scope.updating = true;
                Sensor.forceReadValue(widget.sensor_id);
            };
        }
    };
}]);

