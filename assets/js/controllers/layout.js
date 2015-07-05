
App.controller('LayoutController', ['$scope', 'UserManagement', 'Config', 'gettextCatalog', 'BrowserNotification', 'SocketServer', 'Cache', function ($scope, UserManagement, Config, gettextCatalog, BrowserNotification, SocketServer, Cache) {
    $scope.flashBag  = [];
    $scope.languages = {
        'de': 'Deutsch',
        'en': 'English'
    };

    $scope.currentUser = {};

    UserManagement.loadCurrentUser().success(function(user){
        $scope.currentUser = user;
    });

    $scope.$watch(function() {
        return UserManagement.getCurrentUser();
    }, function (user) {
        $scope.currentUser = user;
    });

    $scope.changeLanguage = function(lang) {
        gettextCatalog.setCurrentLanguage(lang);
        window.location.reload();
    };

    $scope.flushCache = function() {
        Cache.destroy();
    };

    /**
     * @returns {Boolean}
     */
    $scope.isLoggedIn = function () {
        return $scope.currentUser && $scope.currentUser.id > 0;
    };

    $scope.removeFlash = function(index) {
        $scope.flashBag.splice(index, 1);
    };

    $scope.$on('flash', function (type, args) {
        $scope.addFlash(args[0], args[1]);
    });

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

    Config.get('debug').then(function(debug) {
        if (debug) {
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

    $scope.$on('sensor.value', function (eventName, event) {
        var text = '{0}: {1}'.format(event.sensorVo.name, event.valueFormatted);
        BrowserNotification.show(text);
    });
}]);

