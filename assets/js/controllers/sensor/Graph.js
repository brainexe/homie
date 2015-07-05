
App.service('SensorGraph', ['Sensor', 'SensorFormatter', function (Sensor, SensorFormatter) {

    // todo implement stats somehow
    function getStats(series) {
        var max    = Number.MIN_VALUE,
            min    = Number.MAX_VALUE,
            count  = series.data.length,
            sum    = 0,
            values = [],
            value;

        for (var i in series.data) {
            value = series.data[i].y;
            values.push(value);
            sum += value;
            if (value < min) {
                min = value;
            }

            if (value > max) {
                max = value;
            }
        }

        return {
            count: count,
            min: min,
            max: max,
            avg: sum / count,
            median: values.sort()[~~(count/2)]
        };
    }

    function init($scope, element, height, parameters) {
        /**
         * @param sensorValues
         */
        function updateGraph(sensorValues) {
            var oldActive = $scope.graph.series.active;
            sensorValues.active = oldActive;
            $scope.graph.series = sensorValues;
            $scope.graph.update();

            var legend = element.querySelector('.legend')[0];
            legend.innerHTML = '';
            new Rickshaw.Graph.Legend({
                element: legend,
                graph: $scope.graph
            });
        }
        Sensor.getCachedData().success(function(data) {
            $scope.types         = data.types;
            $scope.fromIntervals = data.fromIntervals;

            Sensor.getValues(parameters).success(function (data) {
                $scope.sensors         = data.sensors;
                $scope.activeSensorIds = data.activeSensorIds;
                $scope.currentFrom     = data.currentFrom;
                $scope.stats           = {};

                $scope.graph = new Rickshaw.Graph({
                    element: element.querySelector('.chart'),
                    width: element.offsetWidth - 20,
                    interpolation: 'basis',
                    height: height,
                    min: 'auto',
                    renderer: 'line',
                    series: data.json
                });

                /* todo show stats
                for (var i in data.json) {
                    if ('active' != i) {
                        $scope.stats[data.sensorId] = getStats(data.json[i]);
                    }
                }
                 */

                new Rickshaw.Graph.Axis.Time({graph: $scope.graph});

                new Rickshaw.Graph.Axis.Y({
                    graph: $scope.graph,
                    orientation: 'right',
                    tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
                    element: element.querySelector('.y_axis')
                });

                new Rickshaw.Graph.HoverDetail({
                    graph: $scope.graph,
                    formatter: function (series, x, y) {
                        var type = $scope.types[series.type];
                        var formatter = SensorFormatter.getFormatter(type.formatter);
                        var date = new Date(x * 1000);
                        var dateString = '<span class="date">{0} {1}:{2}</span><br />'.format(
                            date.toDateString(),
                            ("0" + date.getHours()).slice(-2),
                            ("0" + parseInt(date.getMinutes())).slice(-2)
                        );
                        return dateString + series.name + ": " + formatter(y);
                    },
                    // todo yFormatter?
                    xFormatter: function (x) {
                        return new Date(x * 1000).toDateString();
                    }
                });

                new Rickshaw.Graph.Legend({
                    element: element.querySelector('.legend'),
                    graph: $scope.graph
                });

                $scope.graph.render();
            });
        });

        /**
         * @param {Number} sensorId
         * @returns {boolean}
         */
        $scope.isSensorActive = function (sensorId) {
            return $scope.activeSensorIds && $scope.activeSensorIds.indexOf(~~sensorId) > -1;
        };

        /**
         * @param {Number} sensorId
         * @param {Number} from
         */
        $scope.sensorView = function (sensorId, from) {
            sensorId = ~~sensorId;
            $scope.currentFrom = from = from || $scope.currentFrom;

            if (sensorId) {
                if ($scope.isSensorActive(sensorId)) {
                    var index = $scope.activeSensorIds.indexOf(sensorId);
                    $scope.activeSensorIds.splice(index, 1);
                } else {
                    $scope.activeSensorIds.push(sensorId);
                }
            }

            var activeIds  = $scope.activeSensorIds.join(':') || "0";
            var parameters = '?from={0}&save=1'.format($scope.currentFrom);

            Sensor.getValues(activeIds, parameters).success(function (data) {
                updateGraph(data.json);
            });

            return false;
        };
    }

    return {
        init:init
    }
}]);
