
App.ng.service('Widget.sensor', ['Sensor', function(Sensor) {
    return {
        interval: 60 * 5 * 1000,
        render: function ($scope, widget) {
            Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
                $scope.setTitle("{0} ({1})".format(sensorData.sensor.name, sensorData.sensor.type));

                $scope.sensor = sensorData.sensor;
            });
        }
    };
}]);

