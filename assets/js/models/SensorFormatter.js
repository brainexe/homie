
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

    return {
        getFormatter: function(type) {
            switch (type) {
                case 'temperature':
                    return temperatre;
                case 'percentage':
                    return percentage;
                default:
                    return noop;
            }
        }
    };
}]);
