
App.controller('LogoutController', /*@ngInject*/ function($location, UserManagement) {
	UserManagement.logout().success(function() {
		UserManagement.setCurrentUser({});
		$location.path("/login");
	});
});
