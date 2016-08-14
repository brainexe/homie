
App.service('Cache', ['CacheFactory', '$interval', '$rootScope', function(CacheFactory, $interval, $rootScope) {
    var cache = CacheFactory('default', {
        maxAge: 3600 * 1000, // 1h
        deleteOnExpire: 'aggressive',
        storageMode:    'localStorage',
        storagePrefix:  ''
    });

    // do only store plain response in cache when successful
    var oldPut = cache.put.bind(cache);
    cache.put = function put(key, value, options) {
        if (value.length === 4 && value[0] === 200) {
            value = value[1];
        }
        oldPut(key, value, options);
    };

    cache.clear = function(pattern) {
        cache.keys().forEach(function(key) {
            if (key.match && key.match(pattern)) {
                console.debug("Cleaned cache entry: ", key);
                cache.remove(key);
            }
        });
    };

    cache.intervalClear = function(pattern, seconds) {
        $interval(function() {
            cache.clear(pattern);
        }, seconds * 1000);
    };

    cache.closure = function(key, callback, options) {
        var value = cache.get(key);
        if (value !== undefined) {
            return value;
        }

        value = callback();

        cache.put(key, value, options || {});

        return value;
    };

    $rootScope.$on('cache.invalidate', function(event, pattern) {
        cache.clear(pattern);
    });

    return cache;
}]);
