
App.service('_', ['$rootScope', 'gettextCatalog', 'lodash', function ($rootScope, gettextCatalog, lodash) {
    var cachedFunction = lodash.memoize(function(string) {
        return gettextCatalog.getString(string);
    });

    $rootScope.$on('gettextLanguageChanged', function() {
        cachedFunction.cache.clear();
    });

    return cachedFunction;
}]);
