
App.controller('LogoutController', ['$scope', '$location', 'UserManagement', function($scope, $location, UserManagement) {
	UserManagement.logout().success(function() {
		UserManagement.setCurrentUser({});
		$location.path("/index");
	})
}]);
