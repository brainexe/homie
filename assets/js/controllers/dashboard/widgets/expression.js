
App.service('Widget.expression', ['$compile', 'Expression', function($compile, Expression) {
    return {
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
                $compile(element.contents())($scope);
            }

            load(true);
            $scope.reload = function() {
                // todo this method is triggered multiple times...
                Expression.invalidate();
                load(false);
            }
        }
    };
}]);
