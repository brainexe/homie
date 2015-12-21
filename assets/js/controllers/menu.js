
App.controller('MenuController', ['$scope', '$rootScope', '$route', '$location', 'controllers', 'UserManagement', 'UserManagement.Settings', function ($scope, $rootScope, $route, $location, controllers, UserManagement, Settings) {
    $scope.controllers = controllers();
    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }
    });

    $scope.$on('gettextLanguageChanged', function() {
        $scope.controllers = controllers();
        update();
    });

    function update() {
        var user = UserManagement.getCurrentUser();
        var isLoggedIn = UserManagement.isLoggedIn();

        Settings.getAll().success(function(settings) {
            // TODO
        });
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
    }

    $scope.$watch(function() {
        return UserManagement.getCurrentUser(); // todo throw event
    }, update);
    $scope.$on('currentuser.update', function() {
        update();
    });
}]);

