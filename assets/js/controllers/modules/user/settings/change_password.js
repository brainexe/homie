
App.controller('ChangePasswordController', /*@ngInject*/ function ($scope, UserManagement, Flash, _) {
    $scope.changePassword = function() {
        if ($scope.password !== $scope.passwordRepeat) {
            var message = _('The passwords do not match');
            Flash.addFlash(message, Flash.SUCCESS);
        }

        UserManagement.changePassword($scope.oldPassword, $scope.password).success(function () {
            var message = _('The password was changed successfully');
            Flash.addFlash(message, Flash.SUCCESS);
        });
    };
});
