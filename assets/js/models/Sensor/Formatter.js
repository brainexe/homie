
App.service('Sensor.Formatter', [function() {
    function round(number, places) {
        var factor = Math.pow(10, places);
        return (~~Math.round(number * factor)) / factor;
    }

    function temperature(value) {
        return round(value, 2) + '°';
    }

    function percentage(value) {
        return round(value, 2) + '%';
    }

    function barometer(value) {
        return round(value, 2) + 'hPa';
    }

    function noop(value) {
        return round(value, 3);
    }

    function bytes(bytes) {
        return number(bytes) + 'B';
    }

    /**
     * @source http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable
     */
    function number(number) {
        var thresh = 1024;
        if(Math.abs(number) < thresh * 2) {
            return number;
        }
        var units = ['k','M','G','T','P','E','Z','Y'];
        var u = -1;
        do {
            number /= thresh;
            ++u;
        } while(Math.abs(number) >= thresh && u < units.length - 1);

        return number.toFixed(1)+' '+units[u];
    }


    return {
        getFormatter: function(type) {
            switch (type) {
                case 'temperature':
                    return temperature;
                case 'percentage':
                    return percentage;
                case 'barometer':
                    return barometer;
                case 'bytes':
                    return bytes;
                case 'number':
                    return number;
                default:
                    return noop;
            }
        }
    };
}]);
