
App.service('SensorStats', function() {
    return {
        getStats (series) {
            if (!series) {
                return {};
            }
            var result = {
                count: series.data.length / 2,
                min: {value:1e+308, timestamp:null},
                max: {value:-1e+308, timestamp:null}
            };

            var sum    = 0,
                values = [],
                value,
                timestamp;

            for (let i = 0; i < series.data.length; i += 2) {
                timestamp = series.data[i];
                value     = series.data[i + 1];

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
    };
});
