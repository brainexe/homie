
App.service('Widget.expression', ['$compile', 'Expression', function($compile, Expression) {
    return {
        render: function ($scope, widget, element) {
            function load() {
                var expression;
                $scope.reloadButton = widget.reloadButton;

                for (var key in widget.variables) {
                    expression = widget.variables[key];
                    Expression.evaluate(expression, true).success(function(result) {
                        $scope[key] = result;
                    });
                }
                element[0].querySelector('.template').innerHTML = widget.template;
                $compile(element.contents())($scope);
            }

            load();
            $scope.reload = function() {
                Expression.invalidate();
                load();
            }
        }
    };
}]);
