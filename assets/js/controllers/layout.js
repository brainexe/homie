
App.controller('LayoutController', ['$scope', 'UserManagement', 'Config', 'gettextCatalog', 'Cache', function ($scope, UserManagement, Config, gettextCatalog, Cache) {
    $scope.currentUser = {};
    $scope.isLoggedIn  = false;
    $scope.locales     = [];

    var language = 'en_US';
    if (localStorage.getItem('language')) {
        language = localStorage.getItem('language');
    }
    gettextCatalog.cache = Cache;
    gettextCatalog.setCurrentLanguage(language);
    gettextCatalog.loadRemote("/lang/" + language + ".json");

    UserManagement.loadCurrentUser().success(function(user){
        $scope.currentUser = user;
        $scope.isLoggedIn  = user && user.userId > 0;
    });

    Config.getAll().success(function(config) {
        $scope.locales = config.locales;
        if (config.debug) {
            // live reload via "grunt watch"
            var s  = document.createElement('script');
            s.type = 'text/javascript';
            s.src  =' //localhost:35729/livereload.js';
            document.body.appendChild(s);

            // gettext debug mode
            gettextCatalog.debug       = true;
            gettextCatalog.debugPrefix = '?';
        }
    });

    $scope.$on('currentuser.update', function(event, user) {
        $scope.currentUser = user;
        $scope.isLoggedIn  = user && user.userId > 0;
    });

    $scope.$on('cache.clear', function () {
        $scope.flushCache()
    });

    $scope.changeLanguage = function(language) {
        gettextCatalog.loadRemote("/lang/" + language + ".json");
        gettextCatalog.setCurrentLanguage(language);
        localStorage.setItem('language', language);
    };

    $scope.flushCache = function() {
        Cache.destroy();
        //todo $location.reload()
        window.location.reload();
    };
}]);

