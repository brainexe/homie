
App.controller('UserSettingsController', /*@ngInject*/ function ($scope, UserManagementSettings, controllers, _) {
    $scope.settings = {};

    $scope.controllers = controllers().filter(function(controller) {
        return controller.collapsible
    });

    UserManagementSettings.getAll().success(function (result) {
        $scope.settings = result;
    });

    $scope.set = Settings.set;
});
