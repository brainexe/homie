
App.controller('GpioController', ['$scope', 'Gpio', function ($scope, Gpio) {

    $scope.gpios    = {};
    $scope.editMode = false;
    $scope.orderBy  = 'physicalId';

    Gpio.getData().success(function (data) {
        $scope.gpios = data.pins;
    });

    $scope.setOrderBy = function(key) {
        if ($scope.orderBy == key) {
            key = '-' + key;
        }

        $scope.orderBy = key;
    };

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
    $scope.changeMode = function (pin) {
        // todo IN|OUT
        pin.mode = pin.mode ? 0 : 1;
        savePin(pin);
    };
}]);
