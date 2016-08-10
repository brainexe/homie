
App.controller('LayoutController', /*@ngInject*/ function ($scope, UserManagement, Config, gettextCatalog, Cache) {
    $scope.currentUser = {};
    $scope.isLoggedIn  = false;
    $scope.locales     = [];

    var locale = 'en_US';
    if (localStorage.getItem('language')) {
        // todo put lang into settings
        locale = localStorage.getItem('language');
    }
    gettextCatalog.cache = Cache;

    UserManagement.loadCurrentUser().success(function(user){
        $scope.currentUser = user;
        $scope.isLoggedIn  = user && user.userId > 0;
    });

    Config.getAll().success(function(config) {
        $scope.locales = config.locales;
        if (config.debug) {
            // live reload via "grunt watch"
            var script  = document.createElement('script');
            script.type = 'text/javascript';
            script.src  =' //localhost:35729/livereload.js';
            document.body.appendChild(script);

            // gettext debug mode
            gettextCatalog.debug       = true;
            gettextCatalog.debugPrefix = '?';
        }
    });

    $scope.$on('currentuser.update', function(event, user) {
        $scope.currentUser = user;
        $scope.isLoggedIn  = user && user.userId > 0;
    });

    $scope.$on('cache.clear', $scope.flushCache);

    $scope.changeLanguage = function(locale) {
        var langFiles = JSON.parse(LANG_FILES);

        gettextCatalog.loadRemote(langFiles[locale]);
        gettextCatalog.setCurrentLanguage(locale);
        localStorage.setItem('language', locale);
    };

    $scope.flushCache = function() {
        Cache.destroy();
        //todo $location.reload()
        window.location.reload();
    };

    $scope.changeLanguage(locale);
});

