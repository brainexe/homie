
App.Widgets.sensor = {
    interval: 60 * 5 * 1000,
    render: function ($scope, widget) {
        $.get('/sensors/value/', {sensor_id: widget.sensor_id}, function(sensorData) {
            console.log(sensorData)
            $scope.setTitle("{0} ({1})".format(sensorData.sensor.name, sensorData.sensor.type));

            $scope.sensor = sensorData.sensor;
            $scope.$apply();
        });
    }
};
