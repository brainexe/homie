
App.controller('ExpressionController', ['$scope', 'Expression', 'MessageQueue', function ($scope, Expression, MessageQueue) {
	$scope.expressions    = {};
    $scope.editExpression = {actions:[''], conditions:[''], 'new': true};
    $scope.eventNames     = [];
    $scope.crons          = [];

    Expression.getData().success(function(data) {
		$scope.expressions   = data.expressions;
		$scope.eventNames    = data.events;
	});

    $scope.reloadCrons = function() {
        MessageQueue.getJobs('message_queue.cron').success(function() {
            $scope.crons = [];
        });
    };
    $scope.reloadCrons();

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
        Expression.addCron(cron).success(function() {
            $scope.reloadCrons();
        });
    };

    $scope.deleteCron = function(eventId) {
        Expression.deleteEvent(eventId).success(function() {
            delete $scope.crons[eventId];
        });
    };
}]);
