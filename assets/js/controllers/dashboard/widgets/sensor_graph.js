
App.service('Widget.sensor_graph', ['Sensor', 'SensorGraph', '_', function(Sensor, SensorGraph, _) {
    return {
        render: function ($scope, widget) {
            var sensorIds = widget.sensor_ids.map(function(i) {
                return parseInt(i);
            });

            var element = document.querySelector("#widget_" + widget.id + ' .chart_container');

            SensorGraph.init($scope, element, 230, sensorIds.join(':'));
        }
    };
}]);

