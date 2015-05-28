
App.ng.controller('UserTokensController', ['$scope', 'UserManagement.Tokens', '_', function ($scope, Tokens, _) {
    $scope.tokens         = {};
    $scope.roles          = ['login'];
    $scope.availableRoles = [
        'login',
        'register'
    ];

    Tokens.getData().success(function (result) {
        $scope.tokens = result;
    });

    $scope.add = function (roles) {
        Tokens.add(roles).success(function (token) {
            $scope.tokens[token] = roles;
        });
    };

    $scope.revoke = function (token) {
        if (!confirm(_('Delete this token?'))) {
            return;
        }

        Tokens.deleteToken(token).success(function () {
            delete $scope.tokens[token];
        });
    };
}]);
