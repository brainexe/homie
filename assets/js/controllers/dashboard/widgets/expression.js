
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

                var funcs = [];
                Expression.getData(true).success(function(data) {
                    element[0].querySelector('.template').innerHTML = widget.template;
                    $compile(element.contents())($scope);
                    return; // TODO implement
                    funcs = data.functions;
                    for (var i in funcs) {
                        var functionName = ""+funcs[i];
                        $scope[functionName] = function() {
                            arguments = [];
                            var command = functionName + '(' + arguments.join(', ') + ')';
                            Expression.evaluate.call(this, command);
                        };


                    }
                });
            }

            load(true);
            $scope.reload = function() {
                // todo this method is triggered multiple times...
                Expression.invalidate();
                load(false);
            };

            $scope.evaluate = function(expression) {
                Expression.evaluate(expression, false);
            };
        }
    };
}]);
