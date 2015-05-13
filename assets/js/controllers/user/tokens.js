
App.ng.controller('UserTokensController', ['$scope', '$http', function ($scope, $http) {
	$scope.tokens = {};
	$scope.roles  = [];
	$scope.availableRoles = [
		'login',
		'register'
	];

	$http.get('/user/tokens/').success(function(result) {
		$scope.tokens = result;
		$scope.$apply();
	});


	$scope.add = function(roles) {
		console.log(roles);
		console.log($scope.roles);

		$http.post('/user/tokens/', {roles:roles}).success(function(token) {
			$scope.tokens[token] = roles;
			$scope.$apply();
		});
	};

	$scope.revoke = function(token) {
		if (!confirm(gettext('Delete this token?'))) {
			return;
		}

		$http.delete('/user/tokens/' + token).success(function() {
			delete $scope.tokens[token];
			$scope.$apply();
		});
	};
}]);
