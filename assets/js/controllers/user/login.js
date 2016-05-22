App.controller('LoginController', ['$scope', '$location', 'UserManagement', 'UserManagement.TOTP', '_', function ($scope, $location, UserManagement, TOTP, _) {
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
            var message = _("Welcome back {0}!").format(result.username);

            $scope.$broadcast('flash', [message, 'success']);
            UserManagement.setCurrentUser(result);
            $location.path("/dashboard");
        });
    };

    $scope.usernameBlur = function () {
        var username = $scope.username;

        if (!username) {
            $scope.needsOneTimeToken = false;
            return;
        }

        TOTP.needsToken(username).success(function (data) {
            $scope.needsOneTimeToken = data;
        });
    };

    $scope.sendToken = function () {
        if (!$scope.username) {
            return;
        }

        TOTP.sendMail($scope.username).success(function () {
            $scope.$broadcast('flash', [_('Email was sent'), 'success']);
        });
    };
}]);
