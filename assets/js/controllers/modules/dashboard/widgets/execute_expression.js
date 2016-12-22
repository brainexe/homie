
App.service('Widget.execute_expression', /*@ngInject*/ function(Expression) {
    return {
        template: '/templates/widgets/execute_expression.html',
        render ($scope) {
            $scope.value = '';

            $scope.execute = function(value) {
                Expression.evaluate(value).then(function(output) {
                    $scope.output = output.data;
                    console.log(output.data);
                });

                $scope.value = '';
            };
        }
    };
});

