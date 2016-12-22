
App.controller('LogoutController', /*@ngInject*/ function($location, UserManagement) {
	UserManagement.logout().then(function() {
		UserManagement.setCurrentUser({});
		$location.path("/login");
	});
});
