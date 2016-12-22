
App.service('SensorGraph', /*@ngInject*/ function ($uibModal, Sensor, SensorFormatter, SensorDataDecompressor) {
    var rickshaw = Rickshaw.Graph;

    function init($scope, element, height, sensorIds, parameters) {
        var legend = element.querySelector('.legend');

        $scope.$on('sensor.update', function(event, data) {
            if ($scope.isSensorActive(data.sensorId)) {
                update();
            }
        });

        Sensor.getCachedData().then(function(result) {
            let data = data.data;
            $scope.types         = data.types;
            $scope.fromIntervals = data.fromIntervals;
            $scope.sensors       = data.sensors;
            $scope.tags          = aggregateTags(data.sensors);

            Sensor.getValues(sensorIds.join(':'), parameters).then(function (data) {
                data = data.data;
                var yAxisFormatter = getAxisFormatter(data.json);

                $scope.activeSensorIds = Object.keys(data.json).map(Number);
                $scope.ago   = data.ago;
                $scope.to    = data.to;
                $scope.stats = {};

                $scope.graph = new rickshaw({
                    element: element.querySelector('.chart'),
                    width: element.clientWidth - 40,
                    interpolation: 'basis',
                    height: height,
                    min: 'auto',
                    renderer: 'line',
                    series: SensorDataDecompressor(data)
                });
                new rickshaw.Axis.Time({graph: $scope.graph});
                new rickshaw.Axis.Y({
                    graph: $scope.graph,
                    orientation: 'right',
                    element: element.querySelector('.y_axis'),
                    tickFormat: yAxisFormatter
                });
                new rickshaw.ClickDetail({
                    graph: $scope.graph,
                    clickHandler (value){
                        $uibModal.open({
                            templateUrl: '/templates/sensor/sensor_value_detail.html',
                            controller: 'SensorValueDetailModalController',
                            resolve: {
                                value: () => value
                            }
                        }).result.then(update);
                    }
                });
                new rickshaw.HoverDetail({
                    graph: $scope.graph,
                    formatter (series, x, y) {
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
                    xFormatter (x) {
                        return new Date(x * 1000).toDateString();
                    }
                });

                new rickshaw.Legend({
                    element: legend,
                    graph: $scope.graph
                });

                $scope.graph.render();
            });
        });

        function update(parameter = '') {
            var activeIds  = $scope.activeSensorIds.join(':') || "0";
            var parameters = `?from=${$scope.ago}${parameter}`;

            /**
             * @param sensorValues
             */
            function updateGraph(sensorValues) {
                sensorValues.active = $scope.graph.series.active;
                $scope.graph.series = sensorValues;
                $scope.graph.update();

                legend.innerHTML = '';
                new rickshaw.Legend({
                    element: legend,
                    graph: $scope.graph
                });
            }

            Sensor.getValues(activeIds, parameters).then(function (data) {
                updateGraph(decompressData(data.data));
            });
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
         * @param {Number} ago
         */
        $scope.sensorView = function (sensorId, ago) {
            sensorId = ~~sensorId;
            $scope.ago = ago || $scope.ago;

            if (sensorId) {
                if ($scope.isSensorActive(sensorId)) {
                    let index = $scope.activeSensorIds.indexOf(sensorId);
                    $scope.activeSensorIds.splice(index, 1);
                } else {
                    $scope.activeSensorIds.push(sensorId);
                }
            }

            update('&save=1');

            return false;
        };
    }

    function getAxisFormatter(data) {
        var formatter = Rickshaw.Fixtures.Number.formatKMBT;
        var formatterName;
        for (let sensorId in data) {
            let currentFormatterName = data[sensorId].formatter;
            if (formatterName && currentFormatterName !== formatterName) {
                // other formatter is already registered :(
                return formatter;
            }

            formatterName = currentFormatterName;
        }

        if (formatterName) {
            return SensorFormatter.getFormatter(formatterName);
        }

        return formatter;
    }

    // todo use tag service
    function aggregateTags(rawSensors) {
        var tags = {};
        if (!rawSensors) {
            return tags;
        }
        for (let idx in rawSensors) {
            let sensor = rawSensors[idx];
            if (sensor.tags) {
                for (let tag of sensor.tags) {
                    if (!tags[tag]) {
                        tags[tag] = [];
                    }
                    tags[tag].push(sensor);
                }
            }
        }
        return tags;
    }

    return init;
});
