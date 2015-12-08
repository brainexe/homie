
App.service('SensorFormatter', [function() {
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

    /**
     * @source http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable
     */
    function bytes(bytes) {
        var thresh = 1024;
        if(Math.abs(bytes) < thresh) {
            return bytes + ' B';
        }
        var units = ['kB','MB','GB','TB','PB','EB','ZB','YB'];
        var u = -1;
        do {
            bytes /= thresh;
            ++u;
        } while(Math.abs(bytes) >= thresh && u < units.length - 1);

        return bytes.toFixed(1)+' '+units[u];
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
                default:
                    return noop;
            }
        }
    };
}]);
