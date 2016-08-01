
App.service('Sensor.Tags', ['Cache', function(Cache) {
    var CACHE_KEY = 'sensor.tags';

    return {
        // todo lodash
        getTagsFromSensors: function(sensors) {
            return Cache.closure(CACHE_KEY, function() {
                var tags = {};
                sensors.forEach(function(sensor) {
                    sensor.tags.forEach(function (tag) {
                        if (tag) {
                            tags[tag] = true;
                        }
                    })
                });

                return Object.keys(tags);
            });
        }
    }
}]);
