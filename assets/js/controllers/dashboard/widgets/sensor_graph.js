
// todo width
App.service('Widget.sensor_graph', ['Sensor', '_', function(Sensor, _) {
    return {
        interval: 60 * 5 * 1000,
        render: function ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(function(i) {
                return parseInt(i);
            });

            Sensor.getCachedData().success(function(data) {
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

                var element = document.querySelector("#widget_" + widget.id + ' .chart_container');
                $scope.graph = new Rickshaw.Graph({
                    element: element,
                    interpolation: 'basis',
                    min: 'auto',
                    renderer: 'line',
                    series: data.json
                });

                new Rickshaw.Graph.Axis.Time({graph: $scope.graph});
                new Rickshaw.Graph.Axis.Y({
                    graph: $scope.graph,
                    orientation: 'left',
                    tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
                    element: element.getElementsByClassName('y_axis')[0]
                });

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
                //    element: element.getElementsByClassName('legend')[0],
                //    graph: $scope.graph
                //});
                $scope.graph.render();
            });
        }
    };
}]);

