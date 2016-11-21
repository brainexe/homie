
App.directive('sensorSparkLine', /*@ngInject*/ function (Sensor, SensorDataDecompressor) {
    return {
        restrict: 'EA',
        link ($scope, elem) {
                Sensor.getValues($scope.sensorId, $scope.parameters).success(function (data) {
                    $scope.graph = new Rickshaw.Graph({
                        element: elem[0],
                        width: 200,
                        height: 30,
                        interpolation: 'basis',
                        min: 'auto',
                        renderer: 'line',
                        series: SensorDataDecompressor(data)
                    });

                    $scope.graph.render();
                });
        },
        scope: {
            sensorId: "=",
            parameters: "="
        }
    };
});
