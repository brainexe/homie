
App.controller('ChangePasswordController', ['$scope', 'UserManagement', function ($scope, UserManagement) {
    $scope.changePassword = function () {
        if ($scope.password != $scope.passwordRepeat) {
            // todo alert/flash
            return;
        }

        UserManagement.changePassword($scope.oldPassword, $scope.password).success(function () {
            window.location.reload();
        })
    }
}]);
