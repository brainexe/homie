
App.service('Widget.sensor_graph', /*@ngInject*/ function(SensorGraph) {
    return {
        template: '/templates/widgets/sensor_graph.html',
        render ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(Number);
            var element = document.querySelector("#widget_" + widget.id + ' .chart_container');

            SensorGraph($scope, element, 230, sensorIds, `?from=${widget.from || 0}`);
        }
    };
});
