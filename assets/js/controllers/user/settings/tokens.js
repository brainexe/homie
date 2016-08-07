
App.controller('UserTokensController', /*@ngInject*/ function ($scope, UserManagementTokens) {
    $scope.tokens         = {};
    $scope.roles          = ['login'];
    $scope.availableRoles = [
        'register',
        'api'
    ];

    function reload() {
        UserManagementTokens.getData().success(function (result) {
            $scope.tokens = result;
        });
    }

    reload();

    $scope.add = function (roles, name) {
        UserManagementTokens.add(roles, name).success(reload);
    };

    $scope.revoke = function (token) {
        UserManagementTokens.deleteToken(token).success(function () {
            delete $scope.tokens[token];
        });
    };
});
