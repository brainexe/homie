
App.controller('GpioController', ['$scope', 'Gpio', function ($scope, Gpio) {

    $scope.gpios    = {};
    $scope.editMode = false;

    Gpio.getData().success(function (data) {
        $scope.gpios = data.pins;
    });

    /**
     * @param {Object} pin
     */
    function savePin(pin) {
        Gpio.savePin(pin.id, pin.direction, pin.value).success(function (pin) {
            $scope.gpios[pin.id] = pin;
        });
    }

    /**
     * @param {Object} pin
     */
    $scope.saveDescription = function (pin) {
        Gpio.setDescription(pin.id, pin.description);
    };

    /**
     * @param {Object} pin
     */
    $scope.changeValue = function (pin) {
        pin.value = pin.value ? 0 : 1;
        savePin(pin);
    };

    /**
     * @param {Object} pin
     */
    $scope.changeDirection = function (pin) {
        pin.direction = pin.direction ? 0 : 1;
        savePin(pin);
    };
}]);
