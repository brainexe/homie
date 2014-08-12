
App.ng.controller('WebcamController', ['$scope', function($scope) {
	$scope.shots = [];

	$.get('/webcam/', function(data) {

	});

	$scope.$on('webcam.took_photo', function() {
		// TODO ajaxyify!
		window.location.reload();
	});
}]);
