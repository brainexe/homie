
App.controller('GpioController', ['$scope', 'Gpio', 'Nodes', function ($scope, Gpio, Nodes) {
    $scope.gpios    = [];
    $scope.editMode = false;
    $scope.orderBy  = 'physicalId';
    $scope.nodeId   = 0; // current selected node

    var supportedNodes = [
        'arduino',
        'raspberry',
        'particle'
    ];

    Nodes.getData().success(function(data) {
        $scope.nodes = data.nodes.filter(function(node) {
            return supportedNodes.indexOf(node.type) > -1;
        });
    });

    $scope.selectNode = function (node) {
        $scope.nodeId = node.nodeId;

        Gpio.getData(node.nodeId).success(function (data) {
            $scope.gpios = data.pins;
        });
    };

    $scope.toggleEditMode = function () {
        $scope.editMode = !$scope.editMode;
    };

    $scope.setOrderBy = function(key) {
        if ($scope.orderBy == key) {
            key = '-' + key;
        }

        $scope.orderBy = key;
    };

    /**
     * @param {Object} pin
     */
    function savePin(pin, $index) {
        Gpio.savePin($scope.nodeId, pin.id, pin.direction, pin.value).success(function (pin) {
            $scope.gpios[$index] = pin;
        });
    }

    /**
     * @param {Object} pin
     */
    $scope.saveDescription = function (pin) {
        Gpio.setDescription($scope.nodeId, pin.physicalId, pin.description);
    };

    /**
     * @param {Object} pin
     * @param $index
     */
    $scope.changeValue = function (pin, $index) {
        pin.value = pin.value ? 0 : 1;
        savePin(pin, $index);
    };

    /**
     * @param {Object} pin
     * @param $index
     */
    $scope.changeMode = function (pin, $index) {
        // todo IN|OUT
        pin.mode = pin.mode ? 0 : 1;
        savePin(pin, $index);
    };
}]);
