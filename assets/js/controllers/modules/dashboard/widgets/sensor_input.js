
App.service('Widget.sensor_input', /*@ngInject*/ function(Sensor) {
    return {
        template: '/templates/widgets/sensor_input.html',
        render ($scope, widget) {
            $scope.value = '';
            $scope.showSparkLine = widget.showSparkLine;

            Sensor.getSensorData(widget.sensor_id, true).then(function(sensor) {
                $scope.sensor = sensor.data;
            });

            $scope.submit = function(value) {
                $scope.value = '';
                var sensorId = widget.sensor_id;
                Sensor.addValue(sensorId, value);
            };
        }
    };
});
