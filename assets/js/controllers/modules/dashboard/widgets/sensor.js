
App.service('Widget.sensor', /*@ngInject*/ function(Sensor, SensorFormatter, SensorStats) {
    return {
        template: '/templates/widgets/sensor.html',
        render ($scope, widget) {
            $scope.updating = false;
            $scope.format   = SensorFormatter.getFormatter('noop');

            function update() {
                Sensor.getSensorData(widget.sensor_id).then(function(data) {
                    let sensorData = data.data;
                    $scope.format = SensorFormatter.getFormatter(sensorData.formatter);
                    $scope.sensor = sensorData;
                    $scope.value  = sensorData.lastValue;
                    $scope.from   = widget.from;

                    $scope.setTitle(sensorData.name);
                });

                Sensor.getValues(widget.sensor_id, `?from=${widget.from}`).then(function (data) {
                    data = data.data;
                    if (!data.json || !data.json[widget.sensor_id]) {
                        return;
                    }
                    $scope.stats = SensorStats.getStats(data.json[widget.sensor_id]);
                });
            }

            update();

            $scope.$on('sensor.update', function(event, data) {
                if (data.sensorId === widget.sensor_id) {
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
});

