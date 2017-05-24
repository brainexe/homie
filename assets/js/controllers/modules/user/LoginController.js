
App.controller('LoginController', /*@ngInject*/ function ($scope, $location, UserManagement, UserManagementTOTP, Flash, _) {
    if (UserManagement.isLoggedIn()) {
        $location.path("/dashboard");
        return;
    }

    $scope.needsOneTimeToken = false;

    $scope.username = localStorage.getItem('userName');
    $scope.password = '';

    $scope.login = function () {
        let payload = {
            username:       $scope.username,
            password:       $scope.password,
            one_time_token: $scope.one_time_token
        };

        UserManagement.login(payload).then(function (user) {
            if (!user) {
                return;
            }
            let message = _("Welcome back {0}!").format(user.username);
            Flash.addFlash(message, Flash.SUCCESS);

            localStorage.setItem('userName', user.username);
            $location.path("/dashboard");
        });
    };

    var checkOneTimeToken = $scope.usernameBlur = function () {
        let username = $scope.username;

        if (!username) {
            $scope.needsOneTimeToken = false;
            return;
        }

        UserManagementTOTP.needsToken(username).then(function (data) {
            $scope.needsOneTimeToken = data.data;
        });
    };

    $scope.sendToken = function () {
        if (!$scope.username) {
            return;
        }

        UserManagementTOTP.sendMail($scope.username).then(function () {
            Flash.addFlash(_('Email was sent'), Flash.SUCCESS);
        });
    };

    if ($scope.username) {
        checkOneTimeToken();
    }
});
