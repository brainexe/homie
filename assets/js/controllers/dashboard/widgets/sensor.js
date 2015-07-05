
App.service('Widget.sensor', ['Sensor', 'SensorFormatter', function(Sensor, SensorFormatter) {
    function update($scope, widget) {
        Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
            var formatter = SensorFormatter.getFormatter(sensorData.sensorObj.formatter);

            $scope.setTitle("{0}".format(sensorData.sensor.name));

            $scope.sensor = sensorData.sensor;
            $scope.value  = formatter(sensorData.sensor.lastValue);
        });
    }

    return {
        render: function ($scope, widget) {
            update($scope, widget);
        }
    };
}]);

