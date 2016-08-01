
App.controller('FlashController', ['$scope', '$timeout', 'lodash', function ($scope, $timeout, _) {
    $scope.flashBag = [];

    $scope.$on('flash', function (type, args) {
        addFlash(args[0], args[1]);
    });

    $scope.removeFlash = function(index) {
        $scope.flashBag.splice(index, 1);
    };

    /**
     * @param {String} message
     * @param {String} type (success, warning, info, danger)
     */
    function addFlash(message, type) {
        type = type || 'success';

        var item = {
            type:    type,
            message: message
        };

        $scope.flashBag.push(item);

        $timeout(function () {
            _.pull($scope.flashBag, item);
        }, 5000);
    }
}]);
