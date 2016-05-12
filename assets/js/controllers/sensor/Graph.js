
App.service('SensorGraph', ['Sensor', 'Sensor.Formatter', function (Sensor, SensorFormatter) {
    function init($scope, element, height, sensorIds, parameters) {
        $scope.$on('sensor.update', function(event, data) {
            if ($scope.isSensorActive(data.sensorId)) {
                update();
            }
        });

        Sensor.getCachedData().success(function(data) {
            $scope.types         = data.types;
            $scope.fromIntervals = data.fromIntervals;
            $scope.sensors       = data.sensors;
            $scope.tags          = aggregateTags(data.sensors);

            Sensor.getValues(sensorIds.join(':'), parameters).success(function (data) {
                $scope.activeSensorIds = Object.keys(data.json).map(function(i) {return ~~i});
                $scope.ago   = data.ago;
                $scope.to    = data.to;
                $scope.stats = {};

                $scope.graph = new Rickshaw.Graph({
                    element: element.querySelector('.chart'),
                    width: element.clientWidth - 40,
                    interpolation: 'basis',
                    height: height,
                    min: 'auto',
                    renderer: 'line',
                    series: decompressData(data)
                });
                // TODO FIX
                // new Rickshaw.Graph.Axis.Time({graph: $scope.graph});
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
                        var formatter = SensorFormatter.getFormatter(series.formatter || type.formatter);
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
         * @param sensorValues
         */
        function updateGraph(sensorValues) {
            var oldActive = $scope.graph.series.active;
            sensorValues.active = oldActive;
            $scope.graph.series = sensorValues;
            $scope.graph.update();

            var legend = element.querySelector('.legend');
            legend.innerHTML = '';
            new Rickshaw.Graph.Legend({
                element: legend,
                graph: $scope.graph
            });
        }

        function update() {
            var activeIds  = $scope.activeSensorIds.join(':') || "0";
            var parameters = '?from={0}&save=1'.format($scope.ago);

            Sensor.getValues(activeIds, parameters).success(function (data) {
                updateGraph(decompressData(data));
            });
        }

        // todo optimize
        function decompressData(data) {
            var final = [];
            for (var sensorId in data.json) {
                var graphData = [];
                for (var i = 0; i < data.json[sensorId].data.length; i += 2) {
                    graphData.push({
                        x: data.json[sensorId].data[i],
                        y: data.json[sensorId].data[i+1]
                    })
                }
                data.json[sensorId].data = graphData;
                final.push(data.json[sensorId]);
            }

            return final;
        }

        function aggregateTags(rawSensors) {
            var tags = {};
            for (var idx in rawSensors) {
                var sensor = rawSensors[idx];
                if (!sensor.tags) {
                    continue;
                }
                for (var tagId in sensor.tags) {
                    if (!tagId || tagId != ~~ tagId) {
                        continue; // todo ugly fix
                    }
                    var tag = sensor.tags[tagId];
                    if (!tags[tag]) {
                        tags[tag] = [];
                    }
                    tags[tag].push(sensor);
                }
            }
            return tags;
        }

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
        $scope.sensorView = function (sensorId, ago) {
            sensorId = ~~sensorId;
            $scope.ago = ago || $scope.ago;

            if (sensorId) {
                if ($scope.isSensorActive(sensorId)) {
                    var index = $scope.activeSensorIds.indexOf(sensorId);
                    $scope.activeSensorIds.splice(index, 1);
                } else {
                    $scope.activeSensorIds.push(sensorId);
                }
            }

            update();

            return false;
        };
    }

    return {
        init: init
    }
}]);
