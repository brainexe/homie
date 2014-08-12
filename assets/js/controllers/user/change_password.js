
App.ng.controller('ChangePasswordController', ['$scope', function ($scope) {

	$scope.changePassword = function() {
		if (!$scope.password) {
			return;
		}

		if ($scope.password != $scope.password_repeat) {
			return;
		}

		var payload = {
			password: $scope.password
		};

		$.post('/user/change_password/', payload, function() {
			window.location.href = '#dashboard';
		})
	}
}]);