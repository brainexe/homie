
App.controller('OtpController', /*@ngInject*/ function ($scope, UserManagementTOTP) {
    $scope.oneTimePassword = null;

    UserManagementTOTP.getData().then(function (result) {
        $scope.oneTimePassword = result.data;
    });

    $scope.requestToken = function () {
        UserManagementTOTP.request().then(function (result) {
            $scope.oneTimePassword = result.data;
        });
    };

    $scope.deleteToken = function () {
        UserManagementTOTP.deleteToken().then(function () {
            $scope.oneTimePassword = null;
        });
    };
});
