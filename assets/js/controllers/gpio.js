
App.ng.controller('GpioController', ['$scope', function($scope) {

	$scope.gpios    = {};
    $scope.editMode = false;

	$.get('/gpio/', function(data) {
		$scope.gpios = data.pins;
		$scope.$apply();
	});

	/**
	 * @param {Object} pin
	 */
	function savePin(pin) {
		$.post(
			'/gpio/set/{0}/{1}/{2}/'.format(pin.id, pin.direction, pin.value),
			function(pin) {
				$scope.gpios[pin.id] = pin;
			}
		);
	}
	/**
	 * @param {Object} pin
	 */
    $scope.saveDescription = function(pin) {
		$.post(
			'/gpio/description/',
            {
                pinId: pin.id,
                description: pin.description
            }
        ).then(function() {
            $scope.$apply();
        });
	};

	/**
	 * @param {Object} pin
	 */
	$scope.changeValue = function(pin) {
		pin.value = pin.value ? 0 : 1;
		savePin(pin);
	};

	/**
	 * @param {Object} pin
	 */
	$scope.changeDirection = function(pin) {
		pin.direction = pin.direction ? 0 : 1;
		savePin(pin);
	};
}]);
