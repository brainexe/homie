App.controller('MenuController', ['$scope', '$rootScope', '$route', '$location', 'controllers', 'UserManagement', '_', function ($scope, $rootScope, $route, $location, controllers, UserManagement, _) {
    $scope.controllers = controllers;
    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }
    });

    $scope.$watch(function() {
        return UserManagement.getCurrentUser();
    }, function (user) {
        var isLoggedIn = UserManagement.isLoggedIn();

        $scope.menu = $scope.controllers.filter(function (item) {
            if (!item.name) {
                return false;
            }

            if (!isLoggedIn && !item.isPublic) {
                return false;
            } else if (isLoggedIn && item.isPublic === true) {
                return false;
            } else if (item.role && user.roles) {
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

