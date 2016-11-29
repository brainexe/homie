
App.controller('LoginController', /*@ngInject*/ function ($scope, $location, UserManagement, UserManagementTOTP, Flash, _) {
    $scope.needsOneTimeToken = false;

    $scope.username = localStorage.getItem('userName');
    $scope.password = '';

    $scope.login = function () {
        let payload = {
            username:       $scope.username,
            password:       $scope.password,
            one_time_token: $scope.one_time_token
        };

        UserManagement.login(payload).success(function (result) {
            if (!result) {
                return;
            }
            var message = _("Welcome back {0}!").format(result.username);
            Flash.addFlash(message, Flash.SUCCESS);

            localStorage.setItem('userName', result.username);
            $location.path("/dashboard");
        });
    };

    var checkOneTimeToken = $scope.usernameBlur = function () {
        var username = $scope.username;

        if (!username) {
            $scope.needsOneTimeToken = false;
            return;
        }

        UserManagementTOTP.needsToken(username).success(function (data) {
            $scope.needsOneTimeToken = data;
        });
    };

    $scope.sendToken = function () {
        if (!$scope.username) {
            return;
        }

        UserManagementTOTP.sendMail($scope.username).success(function () {
            Flash.addFlash(_('Email was sent'), Flash.SUCCESS);
        });
    };

    if ($scope.username) {
        checkOneTimeToken();
    }
});
