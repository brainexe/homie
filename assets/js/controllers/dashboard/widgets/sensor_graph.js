
App.service('Widget.sensor_graph', ['Sensor', function(Sensor) {
    return {
        interval: 60 * 5 * 1000,
        render: function ($scope, widget) {
            $scope.data = widget.sensorIds;
            $scope.setTitle("Sensors");

            Sensor.getValues('0').success(function (data) {
                $scope.sensors = data.sensors;
                $scope.activeSensorIds = data.activeSensorIds;
                $scope.currentFrom = data.currentFrom;
                $scope.fromIntervals = data.fromIntervals;
                $scope.availableSensors = data.availableSensors;

                $scope.graph = new Rickshaw.Graph({
                    element: document.getElementById("chart"),
                    width: 500,
                    height: 500,
                    interpolation: 'basis',
                    min: 'auto',
                    renderer: 'line',
                    series: data.json
                });

                new Rickshaw.Graph.Axis.Time({graph: $scope.graph});

                var yAxis = new Rickshaw.Graph.Axis.Y({
                    graph: $scope.graph,
                    orientation: 'left',
                    tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
                    element: document.getElementById('yAxis')
                });

                $scope.graph.render();

                new Rickshaw.Graph.HoverDetail({
                    graph: $scope.graph,
                    formatter: function (series, x, y) {
                        var date = new Date(x * 1000);
                        var dateString = '<span class="date">{0} {1}:{2}</span><br />'.format(date.toDateString(), date.getHours(), parseInt(date.getMinutes()));
                        var content = dateString + series.name + ": " + y;
                        return content;
                    },
                    xFormatter: function (x) {
                        return new Date(x * 1000).toDateString();
                    },
                });

                new Rickshaw.Graph.Legend({
                    element: document.querySelector('#legend'),
                    graph: $scope.graph
                });
            });

            //Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
            //    $scope.setTitle("{0} ({1})".format(sensorData.sensor.name, sensorData.sensor.type));
            //
            //    $scope.sensor = sensorData.sensor;
            //});
        }
    };
}]);

