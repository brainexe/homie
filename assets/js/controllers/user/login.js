App.controller('LoginController', ['$scope', 'UserManagement', 'UserManagement.TOTP', '_', function ($scope, UserManagement, TOTP, _) {

    if (UserManagement.isLoggedIn()) {
        window.location.href = '#/dashboard';
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

            window.location.href = '#dashboard';
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
            alert(_('Email was sent'));
        });
    };
}]);
