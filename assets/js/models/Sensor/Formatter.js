
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
        temperature: function (value) {
            return lodash.round(value, 2) + '°';
        },

        percentage: function (value) {
            return lodash.round(value, 2) + '%';
        },

        barometer: function (value) {
            return lodash.round(value, 2) + 'hPa';
        },

        noop: function noop(value) {
            return lodash.round(value, 3);
        },

        bytes: function (bytes) {
            return number(bytes) + 'B';
        }
    };

    return {
        getFormatter: function(type) {
            if (formatter[type]) {
                return formatter[type];
            }

            return formatter.noop;
        }
    };
});
