
App.service('Widget.expression', ['$compile', '$interval', 'Expression', function($compile, $interval, Expression) {
    return {
        template: '/templates/widgets/expression.html',
        render: function ($scope, widget, element) {
            function load(cached) {
                var expression;
                $scope.reloadButton = widget.reloadButton;

                Object.keys(widget.variables).forEach(function(key) {
                    expression = widget.variables[key];
                    Expression.evaluate(expression, cached).success(function(result) {
                        $scope[key] = result;
                    });
                });

                element[0].querySelector('.template').innerHTML = widget.template;
                $compile(element[0].querySelector('.template'))($scope);
            }

            load(true);
            $scope.reload = function() {
                Expression.invalidate();
                load(false);
            };

            $scope.evaluate = function(expression) {
                Expression.evaluate(expression, false);
            };

            if (widget.reloadInterval > 0) {
                $interval($scope.reload.bind(this), widget.reloadInterval * 1000);
            }
        }
    };
}]);
