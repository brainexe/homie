
App.service('Widget.execute_expression', ['Expression', function(Expression) {
    return {
        render: function ($scope, widget) {
            $scope.value = '';

            $scope.execute = function(value) {
                Expression.evaluate(value).success(function(output) {
                    $scope.output = output;
                    console.log(output);
                });

                $scope.value = '';
            }
        }
    };
}]);

