
App.service('Widget.execute_expression', ['Expression', function(Expression) {
    return {
        template: '/templates/widgets/execute_expression.html',
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

