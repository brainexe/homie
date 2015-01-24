
App.ng.controller('LoginController', ['$scope', function ($scope) {

	$scope.needsOneTimeToken = false;

	$scope.login = function() {
		var payload = {
			username: $scope.username,
			password: $scope.password,
			one_time_token: $scope.one_time_token
		};

		$.post('/login/', payload, function(result) {
			if (!result) {
				return;
			}
			App.Layout.$scope.current_user = result;
			App.Layout.$scope.$apply();

			window.location.href = '#dashboard';
		})
	};

	$scope.usernameBlur = function() {
		var username = $scope.username;
		if (!username) {
			$scope.needsOneTimeToken = false;
			return;
		}
		$.get('/login/needsOneTimeToken', {username: username}, function(data) {
			$scope.needsOneTimeToken = data;
			$scope.$apply();
		});
	};

	$scope.sendToken = function() {
		if (!$scope.username) {
			return;
		}

		$.post('/one_time_password/mail/', {
			user_name: $scope.username
		}, function() {
			alert('Email was sent');
		});
	};
}]);
