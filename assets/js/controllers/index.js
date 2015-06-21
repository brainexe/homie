
App.controller('IndexController', ['$scope', 'UserManagement', function ($scope, UserManagement) {
	UserManagement.loadCurrentUser().success(function(user) {
		if (UserManagement.isLoggedIn(user)) {
			window.location.href = '#/dashboard';
		} else {
			window.location.href = '#/login';
		}
	});
}]);
