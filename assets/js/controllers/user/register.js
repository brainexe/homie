
App.ng.controller('RegisterController', ['$scope', function ($scope) {
    if (App.Layout.$scope.isLoggedIn()) {
        window.location.href = '#/dashboard';
        return
    }

	$scope.register = function() {
		var payload = {
			username: $scope.username,
			password: $scope.password
		};

		$.post('/register/', payload, function(user_vo) {
			App.Layout.$scope.current_user = user_vo;
			App.Layout.$scope.$apply();
		})
	}
}]);
