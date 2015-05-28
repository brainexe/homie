
App.ng.controller('RegisterController', ['$scope', 'UserManagement', function ($scope, UserManagement) {
        if (App.Layout.$scope.isLoggedIn()) {
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

                App.Layout.$scope.addFlash(message, 'success');
                App.Layout.$scope.currentUser = userVo;
            });
        }
    }]
);
