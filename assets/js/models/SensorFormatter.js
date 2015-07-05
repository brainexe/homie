
App.service('SensorFormatter', [function() {
    function temperatre(value) {
        return value +'°';
    }

    function percentage(value) {
        return value +'%';
    }

    function noop(value) {
        return value;
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
                    return temperatre;
                case 'percentage':
                    return percentage;
                case 'bytes':
                    return bytes;
                default:
                    return noop;
            }
        }
    };
}]);
