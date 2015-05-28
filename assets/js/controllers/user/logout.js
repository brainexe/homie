
App.ng.controller('LogoutController', ['$scope', 'UserManagement', function($scope, UserManagement) {
	UserManagement.logout().success(function(userVo) {
		App.Layout.$scope.currentUser = userVo;

		window.location.href = '#index';
	})
}]);
