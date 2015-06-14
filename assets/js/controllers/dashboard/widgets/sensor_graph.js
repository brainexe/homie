
App.service('Widget.sensor_graph', ['Sensor', function(Sensor) {
    return {
        interval: 60 * 5 * 1000,
        render: function ($scope, widget) {
            $scope.data = widget.sensorIds;

            //Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
            //    $scope.setTitle("{0} ({1})".format(sensorData.sensor.name, sensorData.sensor.type));
            //
            //    $scope.sensor = sensorData.sensor;
            //});
        }
    };
}]);

