
App.service('Cache', ['CacheFactory', function(CacheFactory) {
    return CacheFactory('defaultCache', {
        maxAge: 15 * 60 * 1000,
        cacheFlushInterval: 85400,
        deleteOnExpire: 'aggressive',
        storageMode: 'localStorage'
    });
}]);
