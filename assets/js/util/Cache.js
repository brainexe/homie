
App.service('Cache', ['CacheFactory', '$interval', '$rootScope', function(CacheFactory, $interval, $rootScope) {
    var cache = CacheFactory('default', {
        maxAge: 3600 * 1000, // 60 minutes
        deleteOnExpire: 'aggressive',
        storageMode:    'localStorage',
        storagePrefix:  ''
    });

    cache.clear = function(pattern) {
        var keys = cache.keys(), key, idx;

        for (idx in keys) {
            key = keys[idx];
            if (key.match(pattern)) {
                cache.remove(key);
            }
        }
    };

    cache.intervalClear = function(pattern, seconds) {
        $interval(function() {
            cache.clear(pattern);
        }, seconds * 1000);
    };

    $rootScope.$on('cache.invalidate', function(event, pattern) {
        cache.clear(pattern);
    });

    return cache;
}]);
