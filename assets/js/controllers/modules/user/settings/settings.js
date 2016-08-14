
App.controller('UserSettingsController', /*@ngInject*/ function ($scope, UserManagementSettings, controllers) {
    $scope.settings = {};

    $scope.controllers = controllers().filter(controller => controller.collapsible);

    UserManagementSettings.getAll().success(function (result) {
        $scope.settings = result;
    });

    $scope.set = UserManagementSettings.set;
});
