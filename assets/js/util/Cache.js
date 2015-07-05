
App.service('Cache', ['CacheFactory', function(CacheFactory) {
    return CacheFactory('defaultCache', {
        maxAge: 15 * 60 * 1000, // 15 minutes
        cacheFlushInterval: 86400,
        deleteOnExpire: 'aggressive',
        storageMode: 'localStorage'
    });
}]);
