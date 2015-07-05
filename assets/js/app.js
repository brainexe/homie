
/**
 * @private
 */
var App = angular.module('homie', [
        'ngDragDrop',
        'ngRoute',
        'ngSanitize',
        'angular-cache',
        'autocomplete',
        'ui.bootstrap',
        'ui.select',
        'ui.sortable',
        'yaru22.angular-timeago',
        'gettext'
    ]).config(['$routeProvider', '$httpProvider', '$provide', function ($routeProvider, $httpProvider, $provide) {
        // needed for translated routes
        $provide.factory('$routeProvider', function () {
            return $routeProvider;
        });
        $provide.factory('$httpProvider', function () {
            return $httpProvider;
        });
    }]).run(['gettextCatalog', '$routeProvider', '$httpProvider', 'controllers', '$rootScope', '$http', 'CacheFactory', function (gettextCatalog, $routeProvider, $httpProvider, controllers, $rootScope, $http, CacheFactory) {
        // TODO: store in cookies  or user setting
        gettextCatalog.setCurrentLanguage('de');

        // init routing
        for (var i in controllers) {
            var metadata = controllers[i];
            $routeProvider.when('/' + metadata.url, metadata);
        }
        $routeProvider.otherwise({redirectTo: '/index'});

        // show error messages as flash
        $httpProvider.defaults.transformResponse.push(function(response, headers, code) {
            if (headers('X-Flash-Type')) {
                $rootScope.$broadcast('flash', [headers('X-Flash-Message'), headers('X-Flash-Type')]);
            }
            return response;
        });
    }]);
