App.controller('ChangePasswordController', ['$scope', 'UserManagement', function ($scope, UserManagement) {

    $scope.changePassword = function () {
        if (!$scope.password) {
            return;
        }

        if ($scope.password != $scope.passwordRepeat) {
            return;
        }

        UserManagement.changePassword(password).success(function () {
            window.location.href = '#dashboard';
        })
    }
}]);
