
App.service('Widget.sensor_graph', /*@ngInject*/ function(SensorGraph, Sensor) {
    return {
        template: '/templates/widgets/sensor_graph.html',
        render ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(Number);
            var element = document.querySelector("#widget_" + widget.id + ' .chart_container');

            $scope.reload = function() {
                sensorIds.forEach(function(sensorId) {
                    Sensor.forceReadValue(sensorId);
                });
            };

            SensorGraph($scope, element, 230, sensorIds, `?from=${widget.from || 0}`);
        }
    };
});
