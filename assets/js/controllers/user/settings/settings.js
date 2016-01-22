
App.controller('UserSettingsController', ['$scope', 'UserManagement.Settings', 'controllers', '_', function ($scope, Settings, controllers, _) {
    $scope.settings = {};

    $scope.controllers = controllers().filter(function(controller) {
        return controller.collapsible
    });

    Settings.getAll().success(function (result) {
        $scope.settings = result;
    });

    $scope.set = function (key, value) {
        Settings.set(key, value);
    };
}]);
