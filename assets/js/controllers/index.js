
App.controller('IndexController', ['$scope', '$location', 'UserManagement', function ($scope, $location, UserManagement) {
	UserManagement.loadCurrentUser().success(function(user) {
		if (UserManagement.isLoggedIn(user)) {
			$location.path("/dashboard");
		} else {
			$location.path("/login");
		}
	});
}]);
