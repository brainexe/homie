
App.service('Sensor.Tags', ['Cache', function(Cache) {
    var CACHE_KEY = 'sensor.tags';

    return {
        getTagsFromSensors: function(sensors) {
            var tags = {};

            sensors.forEach(function(sensor) {
                sensor.tags.forEach(function (tag) {
                    if (tag) {
                        tags[tag] = true;
                    }
                })
            });

            tags = Object.keys(tags);

            Cache.put(CACHE_KEY, tags);

            return tags;
        }
    }
}]);
