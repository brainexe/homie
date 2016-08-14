
App.service('SensorFormatter', /*@ngInject*/ function(lodash) {
    /**
     * @source http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable
     */
    function number(number) {
        const thresh = 1024;
        const units = 'kMGTPEZY';
        var u = -1;
        if (Math.abs(number) < thresh * 2) {
            return number;
        }

        do {
            number /= thresh;
            ++u;
        } while(Math.abs(number) >= thresh && u < units.length - 1);

        return number.toFixed(1) + ' ' + units[u];
    }

    var formatter = {
        temperature:    (value) => lodash.round(value, 2) + '°',
        percentage:     (value) => lodash.round(value, 2) + '%',
        barometer:      (value) => lodash.round(value, 2) + 'hPa',
        noop:           (value) => lodash.round(value, 3),
        bytes:          (bytes) => number(bytes) + 'B',
    };

    return {
        getFormatter (type) {
            if (formatter[type]) {
                return formatter[type];
            }

            return formatter.noop;
        }
    };
});
