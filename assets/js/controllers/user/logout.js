
App.controller('LogoutController', ['$scope', 'UserManagement', function($scope, UserManagement) {
	UserManagement.logout().success(function(userVo) {
		UserManagement.setCurrentUser({});

		window.location.href = '#index';
	})
}]);
