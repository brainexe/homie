
App.controller('ExpressionController', /*@ngInject*/ function ($scope, $q, Expression, MessageQueue, ExpressionFunctions, lodash) {
	$scope.expressions    = {};
    $scope.editMode       = false;
    $scope.editExpression = null;
    $scope.showDisabled   = false;
    $scope.crons          = [];
    $scope.functions      = {};

    $scope.$on(MessageQueue.JOBS_HANDLED, function(event, data) {
        var job = data.job;
        if ($scope.crons[job.jobId]) {
            $scope.crons[job.jobId] = job;
        }
    });

    $scope.reloadCrons = function() {
        return MessageQueue.getJobs('message_queue.cron').then(function(jobs) {
            $scope.crons = jobs;
        });
    };

    $q.all([
        Expression.getData(),
        ExpressionFunctions,
        MessageQueue.getJobs('message_queue.cron')
    ]).then(function(data) {
        $scope.expressions = data[0];
        $scope.functions   = data[1];
        $scope.crons       = data[2].data;
    });

    $scope.newExpression = function () {
        $scope.editExpression = {
            actions:    [''],
            conditions: [''],
            enabled:    true,
            'new':      true
        };
    };

    $scope.searchExpression = function (search) {
        search = search ? search.toLowerCase() : '';

        return lodash.filter($scope.expressions, function(expression) {
            if (!expression.enabled && !$scope.showDisabled) {
                // hide disabled
                return false;
            } else if (!search) {
                // no search criteria -> show
                return true;
            }

            var text = [
                expression.expressionId,
                expression.conditions.join(' '),
                expression.actions.join(' ')
            ].join(' ');

            return text.toLowerCase().includes(search);
        });
    };

    $scope.showDisabledExpression = function (show) {
        $scope.showDisabled = show;
    };

    $scope.save = function(expression) {
        Expression.save(expression).then(function(data) {
            $scope.expressions[expression.expressionId] = data.data;
        });
    };

    $scope.delete = function(expressionId) {
       Expression.deleteExpression(expressionId).then(function() {
            delete $scope.expressions[expressionId];
        });
    };

    $scope.edit = function(expression) {
        expression.conditions = expression.conditions || [''];
        $scope.editExpression = expression;
    };

    $scope.evaluateAction = function(expression, action) {
        Expression.evaluate(action).data(function(result) {
            console.log(result.data);
        });
    };

    $scope.deleteAction = function(index) {
        $scope.editExpression.actions.splice(index, 1);
    };

    $scope.addAction = function(expression) {
        expression.actions = expression.actions || [];
        if (expression.actions.indexOf('') === -1) {
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
        if (expression.conditions.indexOf('') === -1) {
            expression.conditions.push('');
        }
    };

    $scope.deleteParameter = function(expression, key) {
        delete expression.payload[key];
    };

    $scope.addCron = function (cron) {
        Expression.addCron(cron).then(function() {
            $scope.reloadCrons();
        });
    };

    $scope.deleteCron = function (jobId) {
        MessageQueue.deleteJob(jobId).then(function() {
            delete $scope.crons[jobId];
        });
    };
});
