
App.ng.controller('RegisterController', ['$scope', function ($scope) {

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
