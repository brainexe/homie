
App.controller('OtpController', /*@ngInject*/ function ($scope, UserManagementTOTP) {
    $scope.oneTimePassword = null;

    UserManagementTOTP.getData().success(function (result) {
        $scope.oneTimePassword = result;
    });

    $scope.requestToken = function () {
        UserManagementTOTP.request().success(function (result) {
            $scope.oneTimePassword = result;
        });
    };

    $scope.deleteToken = function () {
        UserManagementTOTP.deleteToken().success(function () {
            $scope.oneTimePassword = null;
        });
    };
});
