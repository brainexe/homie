
App.controller('UserController', ['$scope', 'controllers', function ($scope, controllers) {

    $scope.controllers = controllers().filter(function(controller) {
        return controller.collapsible
    });

    $scope.toggle = function(controller) {
        // TODO
    };
}]);
