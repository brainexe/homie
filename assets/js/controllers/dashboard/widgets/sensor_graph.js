
App.service('Widget.sensor_graph', ['SensorGraph', function(SensorGraph) {
    return {
        template: '/templates/widgets/sensor_graph.html',
        render: function ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(function(i) {
                return parseInt(i);
            });

            var element = document.querySelector("#widget_" + widget.id + ' .chart_container');

            SensorGraph.init($scope, element, 230, sensorIds, '?from={0}'.format(widget.from || 0));
        }
    };
}]);
