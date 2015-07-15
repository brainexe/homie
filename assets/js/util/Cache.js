
App.service('Cache', ['CacheFactory', '$rootScope', function(CacheFactory, $rootScope) {

    var cache = CacheFactory('defaultCache', {
        maxAge: 3600 * 1000, // 60 minutes
        deleteOnExpire: 'aggressive',
        storageMode:    'localStorage',
        storagePrefix:  'cache.',
    });

    cache.clear = function(pattern) {
        console.log("invalidate", pattern);
        var keys = cache.keys(), key, idx;
        console.log(keys);

        for (idx in keys) {
            key = keys[idx];
            if (key.match(pattern)) {
                cache.remove(key);
                console.log("remove", key);
            }
        }
    };

    $rootScope.$on('cache.invalidate', function(event, pattern) {
        cache.clear()
    });

    return cache;
}]);
