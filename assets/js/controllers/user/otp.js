
App.ng.controller('OtpController', ['$scope', function ($scope) {
	$scope.one_time_password = null;
	$.get('/one_time_password/', function(result) {
		$scope.one_time_password = result;
		$scope.$apply();
	});

	$scope.requestToken = function() {
		$.post('/one_time_password/request/', function(result) {
			$scope.one_time_password = result;
			$scope.$apply();
		});
	}

	$scope.deleteToken = function() {
		$.post('/one_time_password/delete/', function() {
			$scope.one_time_password = null;
			$scope.$apply();
		});
	}
}]);