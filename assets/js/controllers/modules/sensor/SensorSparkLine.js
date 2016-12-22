
App.directive('sensorSparkLine', /*@ngInject*/ function (Sensor, SensorDataDecompressor, SensorFormatter) {
    return {
        restrict: 'EA',
        link ($scope, elem) {
            Sensor.getValues($scope.sensorId, $scope.parameters).then(function (data) {
                $scope.$on('sensor.update', function(event, data) {
                    if ($scope.sensorId === data.data.sensorId) {
                        console.debug(data.data);
                    }
                });

                var graph = new Rickshaw.Graph({
                    element: elem[0],
                    width: $scope.width || undefined,
                    height: $scope.height || 30,
                    interpolation: 'basis',
                    min: 'auto',
                    renderer: 'line',
                    series: SensorDataDecompressor(data)
                });
                new Rickshaw.Graph.HoverDetail({
                    graph: graph,
                    formatter (series, x, y) {
                        var formatter = SensorFormatter.getFormatter('');
                        var date = new Date(x * 1000);
                        var dateString = '<span class="date">{0} {1}:{2}</span><br />'.format(
                            date.toDateString(),
                            ("0" + date.getHours()).slice(-2),
                            ("0" + parseInt(date.getMinutes())).slice(-2)
                        );
                        return dateString + series.name + ": " + formatter(y);
                    },
                    xFormatter (x) {
                        return new Date(x * 1000).toDateString();
                    }
                });
                // new Rickshaw.Axis.Time({graph: graph});

                graph.render();
            });
        },
        scope: {
            sensorId: "=",
            parameters: "=",
            width: "=",
            height: "="
        }
    };
});
