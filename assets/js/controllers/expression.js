
App.controller('ExpressionController', ['$scope', '$q', 'Expression', 'MessageQueue', 'Expression.Functions', function ($scope, $q, Expression, MessageQueue, ExpressionFunctions) {
	$scope.expressions    = {};
    $scope.editMode       = false;
    $scope.editExpression = null;
    $scope.showDisabled   = false;
    $scope.crons          = [];
    $scope.functions      = [];

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.crons[job.jobId]) {
            $scope.crons[job.jobId] = job;
        }
    });

    $q.all([
        Expression.getData(),
        ExpressionFunctions
    ]).then(function(data) {
        $scope.expressions = data[0].data.expressions;
        $scope.functions   = data[1];
    });

    $scope.reloadCrons = function() {
        return MessageQueue.getJobs('message_queue.cron').success(function(data) {
            $scope.crons = data;
        });
    };

    $scope.newExpression = function () {
        $scope.editExpression = {actions:[''], conditions:[''], 'new': true};
    };

    $scope.showDisabledExpression = function (show) {
        $scope.showDisabled = show;
    };

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

    $scope.evaluateAction = function(expression, action) {
        Expression.evaluate(action).success(function(result) {
            console.log(result);
        });
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

    $scope.setEditMode = function(mode) {
        $scope.editMode = mode;
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

    $scope.deleteCron = function(jobId) {
        MessageQueue.deleteJob(jobId).success(function() {
            delete $scope.crons[jobId];
        });
    };
}]);
