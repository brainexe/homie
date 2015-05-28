
var App = {
    ng: angular.module('homie', [
        'ngDragDrop',
        'ngRoute',
        'ngSanitize',
        'autocomplete',
        'ui.bootstrap',
        'ui.select',
        'yaru22.angular-timeago',
        'gettext'
    ]).config(['$routeProvider', '$httpProvider', '$provide', function ($routeProvider, $httpProvider, $provide) {
        // needed for translated routes
        $provide.factory('$routeProvider', function () {
            return $routeProvider;
        });

        // show error messages as flash
        $httpProvider.defaults.transformResponse.push(function(response, headers, code) {
            if (headers('X-Flash-Type')) {
                App.Layout.$scope.addFlash(headers('X-Flash-Message'), headers('X-Flash-Type'));
            }

            return response;
        });
    }]).run(['gettextCatalog', '$routeProvider', 'controllers', 'SocketServer', function (gettextCatalog, $routeProvider, controllers, SocketServer) {
        // TODO: store in cookies etc
        gettextCatalog.setCurrentLanguage('de');

        // init routing
        for (var i in controllers) {
            var metadata = controllers[i];
            $routeProvider.when('/' + metadata.url, metadata);
        }

        $routeProvider.otherwise({redirectTo: '/index'});
    }])
};
