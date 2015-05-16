
App.ng.controller('MenuController', ['$scope', '$route', '$location', 'controllers', 'gettextCatalog', function ($scope, $route, $location, controllers, gettextCatalog) {
    $scope.controllers = controllers;

    $scope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.name) {
            document.title = current.$$route.name;
        }
    });

	var translated = false;
	$scope.$parent.$watch('currentUser', function(user) {
		var is_logged_in = $scope.$parent.isLoggedIn();

		$scope.menu = $scope.controllers.filter(function(item) {
			if (!item.name) {
				return false;
			}

			if (!translated) {
				item.name = gettextCatalog.getString(item.name);
			}

			if (!is_logged_in && !item.isPublic) {
				return false;
			} else if (is_logged_in && item.isPublic === true) {
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

		translated = true;
	});
}]);

