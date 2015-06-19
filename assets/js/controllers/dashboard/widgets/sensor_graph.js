
App.service('Widget.sensor_graph', ['Sensor', '_', function(Sensor, _) {
    return {
        interval: 60 * 5 * 1000,
        render: function ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(function(i) {
                return parseInt(i);
            });

            Sensor.getAll().success(function(data) {
                var names = [];
                for (var i in data.sensors) {
                    if (sensorIds.indexOf(data.sensors[i].sensorId) >= 0) {
                        names.push(data.sensors[i].name);
                    }
                }
                var name = widget.title || _('Sensors');

                $scope.setTitle(name + ' - ' + names.join(', '));
            });

            Sensor.getValues(sensorIds.join(':')).success(function (data) {
                $scope.sensors          = data.sensors;
                $scope.activeSensorIds  = data.activeSensorIds;
                $scope.currentFrom      = data.currentFrom;
                $scope.fromIntervals    = data.fromIntervals;
                $scope.availableSensors = data.availableSensors;

                $scope.graph = new Rickshaw.Graph({
                    element: document.getElementById("widget_" + widget.id),
                    width: 500,
                    height: 150,
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
                    }
                });

                //new Rickshaw.Graph.Legend({
                //    element: document.querySelector('#legend'),
                //    graph: $scope.graph
                //});
            });

            //Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
            //    $scope.setTitle("{0} ({1})".format(sensorData.sensor.name, sensorData.sensor.type));
            //
            //    $scope.sensor = sensorData.sensor;
            //});
        }
    };
}]);

