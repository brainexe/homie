
App.controller('UserTokensController', ['$scope', 'UserManagement.Tokens', '_', function ($scope, Tokens, _) {
    $scope.tokens         = {};
    $scope.roles          = ['login'];
    $scope.availableRoles = [
        'register',
        'api'
    ];

    function reload() {
        Tokens.getData().success(function (result) {
            $scope.tokens = result;
        });
    }

    reload();

    $scope.add = function (roles, name) {
        Tokens.add(roles, name).success(function (token) {
            reload();
        });
    };

    $scope.revoke = function (token) {
        Tokens.deleteToken(token).success(function () {
            delete $scope.tokens[token];
        });
    };
}]);
