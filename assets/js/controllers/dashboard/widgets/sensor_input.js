
App.service('Widget.sensor_input', ['Sensor', function(Sensor) {
    return {
        render: function ($scope, widget) {
            $scope.value = '';

            Sensor.getSensorData(widget.sensor_id, true).success(function(data) {
                $scope.placeholder = data.sensor.lastValue;
            });

            $scope.submit = function(value) {
                var sensorId = widget.sensor_id;
                Sensor.addValue(sensorId, value);
            }
        }
    };
}]);
