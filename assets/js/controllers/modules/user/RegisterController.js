
App.controller('RegisterController', /*@ngInject*/ function ($scope, $location, UserManagement, Flash, _) {
    if (UserManagement.isLoggedIn()) {
        $location.path("/dashboard");
        return;
    }

    $scope.register = function () {
        var payload = {
            username: $scope.username,
            password: $scope.password
        };

        UserManagement.register(payload).then(function (data) {
            let userVo = data.data;
            var message = _("Welcome {0}!").format(userVo.username);
            Flash.addFlash(message, Flash.SUCCESS);

            UserManagement.setCurrentUser(userVo);
            $location.path("/dashboard");
        });
    };
});
