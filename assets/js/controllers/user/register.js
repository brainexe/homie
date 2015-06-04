
App.controller('RegisterController', ['$scope', 'UserManagement', function ($scope, UserManagement) {
        if (UserManagement.isLoggedIn()) {
            window.location.href = '#/dashboard';
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
            });
        }
    }]
);
