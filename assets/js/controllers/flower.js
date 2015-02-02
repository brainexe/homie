
App.ng.controller('FlowerController', ['$scope', function ($scope) {
	$.get('/flower/', function(data) {
		$scope.humidity     = data.humidity;
		$scope.waterEnabled = data.humidity <= 50;
		$scope.$apply();
	});

	$scope.water = function() {
		$scope.waterEnabled = false;

		$.post('/flower/water/', function(data) {
			$scope.waterEnabled = true;
		});
	};
}]);
