
App.controller('IndexController', /*@ngInject*/ function ($location, UserManagement) {
	UserManagement.loadCurrentUser().then(function(user) {
		if (UserManagement.isLoggedIn(user.data)) {
			$location.path("/dashboard");
		} else {
			$location.path("/login");
		}
	});
});
