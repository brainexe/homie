
App.controller('IndexController', /*@ngInject*/ function ($location, UserManagement) {
	UserManagement.loadCurrentUser().success(function(user) {
		if (UserManagement.isLoggedIn(user)) {
			$location.path("/dashboard");
		} else {
			$location.path("/login");
		}
	});
});