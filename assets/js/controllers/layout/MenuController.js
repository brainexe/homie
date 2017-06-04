
App.controller('MenuController', /*@ngInject*/ function ($scope, $q, $location, controllers, UserManagement, UserManagementSettings, Config) {
    $scope.menu = [];

    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }

        if (current.$$route && !current.$$route.isPublic) {
            UserManagement.loadCurrentUser().then(function (user) {
                if (!UserManagement.isLoggedIn(user.data)) {
                    $location.path("/login");
                }
            });
        }
    });

    $scope.$on('gettextLanguageChanged', update);
    $scope.$on('currentuser.authorized', update);
    $scope.$on('currentuser.logout', update);

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

    function update() {
        $q.all([
            Config.getAll(),
            UserManagement.loadCurrentUser(),
            UserManagementSettings.getAll()
        ]).then(function(data) {
            var config     = data[0];
            var user       = data[1].data;
            var settings   = data[2].data;
            var isLoggedIn = UserManagement.isLoggedIn(user);
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
});

