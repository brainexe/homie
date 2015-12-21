
App.service('Widget.sensor_input', ['Sensor', function(Sensor) {
    return {
        render: function ($scope, widget) {
            $scope.value = '';

            Sensor.getSensorData(widget.sensor_id, true).success(function(sensor) {
                $scope.sensor      = sensor;
                $scope.placeholder = sensor.lastValue;
            });

            $scope.submit = function(value) {
                var sensorId = widget.sensor_id;
                Sensor.addValue(sensorId, value);
            }
        }
    };
}]);
