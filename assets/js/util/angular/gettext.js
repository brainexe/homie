
App.service('_', /*@ngInject*/ function ($rootScope, gettextCatalog, lodash) {
    var cachedFunction;

    if (lodash) { // todo matze
        cachedFunction = lodash.memoize(gettextCatalog.getString.bind(gettextCatalog));
    } else {
        gettextCatalog.getString.bind(gettextCatalog);
    }

    $rootScope.$on('gettextLanguageChanged', function() {
        console.debug('Clear language cache');
        cachedFunction.cache.clear();
    });

    return cachedFunction;
});

App.run(["$rootScope", "gettextCatalog", "Cache", "Config", function($rootScope, gettextCatalog, Cache, Config) {
    // a bit of magic: the lang json files are passed into app.js in grunt task,
    // to be able to append hah of file as effective cache breaking
    var langFiles = JSON.parse(LANG_FILES);

    gettextCatalog.cache = Cache;

    Config.getAll().then(function(config) {
        if (config.data.debug) {
            gettextCatalog.debug       = true;
            gettextCatalog.debugPrefix = '?';
        }
    });

    var storage = localStorage;
    var locale = 'en_US';
    if (storage.getItem('language')) {
        // todo put lang into UserSettings
        locale = storage.getItem('language');
    }

    $rootScope.changeLanguage = function(locale) {
        console.debug('Changed locale to ', locale);

        gettextCatalog.currentLanguage = locale;
        gettextCatalog.loadRemote(langFiles[locale]);
        storage.setItem('language', locale);
    };

    $rootScope.changeLanguage(locale);
}]);
