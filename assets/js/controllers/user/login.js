
App.controller('LoginController', /*@ngInject*/ function ($scope, $location, UserManagement, UserManagementTOTP, _) {
    if (UserManagement.isLoggedIn()) {
        $location.path("/dashboard");
        return
    }

    $scope.needsOneTimeToken = false;

    $scope.login = function () {
        var payload = {
            username: $scope.username,
            password: $scope.password,
            one_time_token: $scope.one_time_token
        };

        UserManagement.login(payload).success(function (result) {
            if (!result) {
                return;
            }
            var message = _("Welcome back {0}!").format(result.username);

            $scope.$broadcast('flash', [message, 'success']);
            $location.path("/dashboard");
        });
    };

    $scope.usernameBlur = function () {
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
            $scope.$broadcast('flash', [_('Email was sent'), 'success']);
        });
    };
});
