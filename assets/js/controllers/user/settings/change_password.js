
App.controller('ChangePasswordController', ['$scope', 'UserManagement', '_', function ($scope, UserManagement, _) {
    $scope.changePassword = function() {
        if ($scope.password != $scope.passwordRepeat) {
            $scope.$broadcast('flash', [_('The passwords do not match'), 'error']);
            return;
        }

        UserManagement.changePassword($scope.oldPassword, $scope.password).success(function () {
            $scope.$broadcast('flash', [_('The password was changed successfully'), 'success']);
        })
    }
}]);
