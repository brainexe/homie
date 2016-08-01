
App.controller('MenuController', ['$scope', '$q', '$location', 'controllers', 'UserManagement', 'UserManagement.Settings', 'Config', 'lodash', function ($scope, $q, $location, controllers, UserManagement, Settings, Config, lodash) {
    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }

        if (current.$$route && !current.$$route.isPublic) {
            UserManagement.loadCurrentUser().success(function (user) {
                if (!UserManagement.isLoggedIn(user)) {
                    $location.path("/login");
                }
            });
        }
    });

    $scope.$on('gettextLanguageChanged', function() {
        update();
    });

    $scope.$on('currentuser.update', function() {
        update();
    });

    function update() {
        $q.all([
            Config.getAll(),
            UserManagement.loadCurrentUser(),
            Settings.getAll()
        ]).then(function(data) {
            var config     = data[0].data;
            var user       = data[1].data;
            var settings   = data[2].data;
            var isLoggedIn = user && user.userId > 0;
            var disabled   = settings.hiddenMenus;

            $scope.menu = controllers().filter(function (item) {
                if (!checkItem(item, disabled, config)) {
                    return false;
                } else if (!isLoggedIn && !item.isPublic) {
                    return false;
                } else if (isLoggedIn && item.isPublic === true) {
                    return false;
                } else if (item.role && user.roles) {
                    return user.roles.indexOf(item.role) > -1;
                }

                return true;
            });
        });
    }

    function checkItem(item, disabled, config) {
        if (!item.name) {
            // hidden menu entry without name
            return false;
        }

        if (disabled && disabled[item.controller]) {
            // disabled via settings
            return false;
        }

        return !(item.checkConfig && !item.checkConfig(config));
    }
}]);

