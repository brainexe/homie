
App.service('SensorTags', /*@ngInject*/ function(Cache) {
    const CACHE_KEY = 'sensor.tags';

    return {
        getTagsFromSensors (sensors) {
            return Cache.closure(CACHE_KEY, function() {
                var tags = {};
                sensors.forEach(function(sensor) {
                    sensor.tags.forEach(function (tag) {
                        if (tag) {
                            tags[tag] = true;
                        }
                    });
                });

                return Object.keys(tags);
            });
        }
    }
});
