
App.controller('RegisterController', /*@ngInject*/ function ($scope, $location, UserManagement, _) {
    if (UserManagement.isLoggedIn()) {
        $location.path("/dashboard");
        return
    }

    $scope.register = function () {
        var payload = {
            username: $scope.username,
            password: $scope.password
        };

        UserManagement.register(payload).success(function (userVo) {
            var message = _("Welcome {0}!").format(userVo.username);

            $scope.$broadcast('flash', [message, 'success']);
            UserManagement.setCurrentUser(userVo);
            $location.path("/dashboard");
        });
    }
});
