
App.controller('UserTokensController', /*@ngInject*/ function ($scope, UserManagementTokens) {
    $scope.tokens         = {};
    $scope.roles          = ['login'];
    $scope.availableRoles = [
        'register',
        'api'
    ];

    function reload() {
        UserManagementTokens.getData().then(function (result) {
            $scope.tokens = result.data;
        });
    }

    reload();

    $scope.add = function (roles, name) {
        UserManagementTokens.add(roles, name).then(reload);
    };

    $scope.revoke = function (token) {
        UserManagementTokens.deleteToken(token).then(function () {
            delete $scope.tokens[token];
        });
    };
});
