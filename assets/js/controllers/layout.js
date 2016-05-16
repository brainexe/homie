
App.controller('LayoutController', ['$scope', 'UserManagement', 'Config', 'gettextCatalog', 'SocketServer', 'Cache', function ($scope, UserManagement, Config, gettextCatalog, SocketServer, Cache) {
    $scope.flashBag    = [];
    $scope.currentUser = {};
    $scope.locales     = [];

    var language = 'en';
    if (localStorage.getItem('language')) {
        language = localStorage.getItem('language');
    }
    gettextCatalog.cache = Cache;
    gettextCatalog.setCurrentLanguage(language);
    gettextCatalog.loadRemote("/lang/" + language + ".json");

    UserManagement.loadCurrentUser().success(function(user){
        $scope.currentUser = user;
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
    });

    $scope.$on('flash', function (type, args) {
        $scope.addFlash(args[0], args[1]);
    });

    $scope.$on('cache.clear', function () {
        $scope.flushCache()
    });

    $scope.changeLanguage = function(lang) {
        gettextCatalog.setCurrentLanguage(lang);
        localStorage.setItem('language', lang);
        //window.location.reload();
    };

    $scope.flushCache = function() {
        Cache.destroy();
        window.location.reload();
    };

    /**
     * @returns {Boolean}
     */
    $scope.isLoggedIn = function () {
        return $scope.currentUser && $scope.currentUser.userId > 0;
    };

    $scope.removeFlash = function(index) {
        $scope.flashBag.splice(index, 1);
    };

    /**
     * @param {String} message
     * @param {String} type (success, warning, info, danger)
     */
    $scope.addFlash = function (message, type) {
        type = type || 'success';

        var item = {
            type:    type,
            message: message
        };

        $scope.flashBag.push(item);

        window.setTimeout(function () {
            var index = $scope.flashBag.indexOf(item);

            if (index > -1) {
                $scope.flashBag.splice(index, 1);
                $scope.$apply();
            }
        }, 5000);
    };
}]);

