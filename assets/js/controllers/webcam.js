
App.ng.controller('WebcamController', ['$scope', function($scope) {
	$scope.shots = [];

	$.get('/webcam/', function(data) {
		$scope.shots = data.shots;
		$scope.$apply();
	});

	$scope.takeShot = function() {
		$.post('/webcam/take/');
	};

	$scope.removeShot = function(index) {
		var shot = $scope.shots[index];
		shot.deleting = true;
		$.post('/webcam/delete/', {shotId:shot.webPath}, function() {
			$scope.shots.slice(index, 1);
			$scope.$apply();
		});
	};

	$scope.$on('webcam.took_photo', function(data) {
		$scope.shots.push(data);
		$scope.$apply();
	});
}]);
