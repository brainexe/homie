App.ng.controller('AdminSensorsController', ['$scope', '$modalInstance', function($scope, $modalInstance) {

    $scope.sensors = [];
    $scope.types   = {};

    $.get('/sensors/', function(data) {
        $scope.sensors = data.sensors;
        $scope.types   = data.types;
        $scope.$apply();
    });

	$scope.deleteSensor = function(sensor) {
        $.post('/sensors/delete/', {
            'sensorId': sensor.sensorId
        }, function() {
            var index = $scope.sensors.indexOf(sensor);
            $scope.sensors.splice(index, 1);
            $scope.$apply();
        });
	};

	$scope.close = function() {
		$modalInstance.close();
	}
}]);
