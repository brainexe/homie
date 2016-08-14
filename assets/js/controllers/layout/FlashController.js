
App.controller('FlashController', /*@ngInject*/ function ($scope, $timeout, lodash) {
    $scope.flashBag = [];

    /**
     * @param {String} message
     * @param {String} type (success, warning, info, danger)
     */
    function addFlash(message, type = 'success') {
        var item = {type, message};

        $scope.flashBag.push(item);

        $timeout(function () {
            lodash.pull($scope.flashBag, item);
        }, 5000);
    }

    $scope.$on('flash', (type, args) => addFlash(args[0], args[1]));

    $scope.removeFlash = index => $scope.flashBag.splice(index, 1);
});
