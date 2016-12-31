
App.controller('ExpressionVariablesController', /*@ngInject*/ function ($scope, Expression) {
    $scope.variables = {};
    $scope.newKey    = '';
    $scope.newValue  = '';

    Expression.getVariables().then(function (variables) {
        $scope.variables = variables.data;
    });

    $scope.setVariable = function (key, value) {
        Expression.setVariable(key, value).then(function () {
            $scope.variables[key] = value;
        });
    };

    $scope.deleteVariable = function (key) {
        Expression.deleteVariable(key).then(function () {
            delete $scope.variables[key];
        });
    };
});
