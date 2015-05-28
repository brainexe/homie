App.ng.controller('MenuController', ['$scope', '$rootScope', '$route', '$location', 'controllers', '_', function ($scope, $rootScope, $route, $location, controllers, _) {
    $scope.controllers = controllers;
    console.log($rootScope);
    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }
    });

    $scope.$parent.$watch('currentUser', function (user) {
        var isLoggedIn = $scope.$parent.isLoggedIn();

        $scope.menu = $scope.controllers.filter(function (item) {
            if (!item.name) {
                return false;
            }

            if (!isLoggedIn && !item.isPublic) {
                return false;
            } else if (isLoggedIn && item.isPublic === true) {
                return false;
            } else if (item.role) {
                for (var i = 0; i < user.roles.length; i++) {
                    if (user.roles[i] == item.role) {
                        return true;
                    }
                }
                return false;
            }

            return true;
        });
    });
}]);

