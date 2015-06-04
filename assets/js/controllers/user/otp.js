App.controller('OtpController', ['$scope', 'UserManagement.TOTP', function ($scope, TOTP) {
    $scope.oneTimePassword = null;

    TOTP.getData().success(function (result) {
        $scope.oneTimePassword = result;
    });

    $scope.requestToken = function () {
        TOTP.request().success(function (result) {
            $scope.oneTimePassword = result;
        });
    };

    $scope.deleteToken = function () {
        TOTP.deleteToken().success(function () {
            $scope.oneTimePassword = null;
        });
    }
}]);
