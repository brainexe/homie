
App.service('Widget.expression', /*@ngInject*/ function($compile, $interval, Expression) {
    return {
        template: '/templates/widgets/expression.html',
        render ($scope, widget, element) {
            var elem = element[0].querySelector('.template');

            function load(cached) {
                $scope.reloadButton = widget.reloadButton;

                Object.keys(widget.variables).forEach(function(key) {
                    var expression = widget.variables[key];
                    Expression.evaluate(expression, cached).then(function(result) {
                        $scope[key] = result.data;
                    });
                });

                elem.innerHTML = widget.template;
                $compile(elem)($scope);
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
                var interval = $interval(() => $scope.reload(), widget.reloadInterval * 1000);
                $scope.$on('$destroy', function() {
                    $interval.cancel(interval);
                });
            }
        }
    };
});
