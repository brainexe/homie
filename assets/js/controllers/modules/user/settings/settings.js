
App.controller('UserSettingsController', /*@ngInject*/ function ($scope, UserManagementSettings, controllers) {
    $scope.settings = {};

    $scope.controllers = controllers().filter(controller => controller.collapsible);

    UserManagementSettings.getAll().then(function (result) {
        $scope.settings = result.data;
    });

    $scope.set = UserManagementSettings.set;
});
