
App.controller('IndexController', ['$scope', 'UserManagement', function ($scope, UserManagement) {
	if (UserManagement.isLoggedIn()) {
		window.location.href = '#/dashboard';
	} else {
		window.location.href = '#/login';
	}
}]);
