
App.controller('ExpressionController', ['$scope', 'Expression', function ($scope, Expression) {
	$scope.input_control  = [];
	$scope.expressions    = {};
    $scope.editExpression = {actions:['']};
    $scope.eventNames     = [];
    $scope.timers         = [];

    $scope.suggestionsCondition = [];
    $scope.suggestionsActions   = [];

    $scope.autocompleteAction = function(typed) {
        $scope.suggestionsActions = [].concat(
            $scope.input_control.map(function(regexp) {
                return 'input("' + regexp.substr(2, regexp.length - 4) + '")';
            }),
            Object.keys($scope.timers).map(function(key) {
                var timer = $scope.timers[key];
                return 'isTiming("' + timer.event.event.timingId + '")';
            })
        );
    };

    $scope.autocompleteCondition = function(typed) {
        $scope.suggestionsCondition = [].concat(
            $scope.eventNames.map(function(eventname) {
                return 'eventName == "' + eventname + '"';
            }),
            Object.keys($scope.timers).map(function(key) {
                var timer = $scope.timers[key];
                return 'isTiming("' + timer.event.event.timingId + '")';
            })
        );
    };

    Expression.getData().success(function(data) {
		$scope.expressions   = data.expressions;
		$scope.input_control = data.input_control;
		$scope.eventNames    = data.events;
		$scope.timers        = data.timers;
	});

    $scope.save = function(expression) {
        Expression.save(expression).success(function(data) {
            $scope.expressions[data.expressionId] = data;
        });
    };

    $scope.delete = function(expressionId) {
       Expression.deleteExpression(expressionId).success(function() {
            delete $scope.expressions[expressionId];
        });
    };

    $scope.edit = function(expression) {
        $scope.editExpression = expression;
    };

    $scope.deleteAction = function(index) {
        $scope.editExpression.actions.splice(index, 1);
    };

    $scope.addAction = function(expression) {
        expression.actions = expression.actions || [];
        if (expression.actions.indexOf('') == -1) {
            expression.actions.push('');
        }
    };

    $scope.addTimer = function(timer) {
        Expression.addTimer(timer).success(function(data) {
            $scope.timers = data.timers;
        });
    };
}]);
