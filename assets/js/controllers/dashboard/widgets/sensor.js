
App.service('Widget.sensor', ['Sensor', '$rootScope', 'SensorFormatter', function(Sensor, $rootScope, SensorFormatter) {

    function getStats(series) {
        if (!series) {
            return {};
        }
        var result = {
            count: series.data.length / 2,
            min: {value:Number.MAX_VALUE, timestamp:null},
            max: {value:Number.MIN_VALUE, timestamp:null}
        };

        var sum    = 0,
            values = [],
            value,
            timestamp;

        for (var i = 0; i < series.data.length; i += 2) {
            timestamp = series.data[i];
            value     = series.data[i +1];

            values.push(value);
            sum += value;
            if (value < result.min.value) {
                result.min.value = value;
                result.min.timestamp = timestamp;
            }

            if (value > result.max.value) {
                result.max.value = value;
                result.max.timestamp = timestamp;
            }
        }

        result.latest     = values.pop();
        result.lastChange = result.latest - values.pop();
        result.avg        = sum / result.count;
        result.median     = values.sort()[~~(result.count/2)];

        return result;
    }

    return {
        render: function ($scope, widget) {
            $scope.updating = false;
            $scope.format   = SensorFormatter.getFormatter('noop');

            function update() {
                Sensor.getSensorData(widget.sensor_id).success(function(sensorData) {
                    $scope.format = SensorFormatter.getFormatter(sensorData.sensorObj.formatter);

                    $scope.setTitle("{0}".format(sensorData.sensor.name));

                    $scope.sensor = sensorData.sensor;
                    $scope.value  = sensorData.sensor.lastValue;
                });

                Sensor.getValues(widget.sensor_id, '?from={0}'.format(~~widget.from)).success(function (data) {
                    if (!data.json) {
                        return;
                    }
                    $scope.stats = getStats(data.json[0]);
                });
            }

            update();

            $rootScope.$on('sensor.update', function() {
                $scope.updating = false;
                update();
            });

            $scope.reload = function() {
                $scope.updating = true;
                Sensor.forceReadValue(widget.sensor_id);
            };
        }
    };
}]);

