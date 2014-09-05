
App.ng.controller('WebcamController', ['$scope', function($scope) {
	$scope.shots = [];

	$.get('/webcam/', function(data) {
		$scope.shots = data.shots;
		$scope.$apply();
	});

	$scope.takeShot = function() {
		$.post('/webcam/take/', function() {
		});
	};

	$scope.removeShot = function(index) {
		var shot = $scope.shots[index];
		$.post('/webcam/delete/', {id:shot.id}, function() {
			delete $scope.shots[index];
			$scope.$apply();
		});
	};

	$scope.$on('webcam.took_photo', function(data) {
		$scope.shots.push(data);
		$scope.$apply();
	});
}]);
