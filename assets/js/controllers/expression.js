
App.controller('ExpressionController', ['$scope', 'Expression', function ($scope, Expression) {
	$scope.input_control  = [];
	$scope.expressions    = {};
    $scope.editExpression = {actions:[''], conditions:['']};
    $scope.eventNames     = [];
    $scope.crons          = [];

    Expression.getData().success(function(data) {
		$scope.expressions   = data.expressions;
		$scope.input_control = data.input_control;
		$scope.eventNames    = data.events;
		$scope.crons         = data.crons;
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
        expression.conditions = expression.conditions || [''];
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
    $scope.deleteCondition = function(index) {
        $scope.editExpression.conditions.splice(index, 1);
    };

    $scope.addCondition = function(expression) {
        expression.conditions = expression.conditions || [];
        if (expression.conditions.indexOf('') == -1) {
            expression.conditions.push('');
        }
    };

    $scope.addCron = function(cron) {
        Expression.addCron(cron).success(function(data) {
            $scope.crons = data.crons;
        });
    };

    $scope.deleteCron = function(eventId) {
        // TODO delete cron
    };
}]);
