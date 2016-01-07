
App.controller('ExpressionController', ['$scope', '$rootScope', '$q', 'Expression', 'MessageQueue', function ($scope, $rootScope, $q, Expression, MessageQueue) {
	$scope.expressions    = {};
    $scope.editExpression = {actions:[''], conditions:[''], 'new': true};
    $scope.crons          = [];
    $scope.functions      = [];

    $scope.reloadCrons = function() {
        return MessageQueue.getJobs('message_queue.cron').success(function(data) {
            $scope.crons = data;
        });
    };

    function generateParameterList(array) {
        var parameterList = [];
        array.forEach(function(parameter) {
            parameterList.push('"' + parameter + '"');
        });
        return parameterList.join(', ');
    }

    $q.all([
        $scope.reloadCrons(),
        Expression.getData(),
        Expression.getEvents(),
        Expression.getFunctions()
    ]).then(function(data) {
        var crons       = data[0].data;
        var expressions = data[1].data;
        var events      = data[2].data;
        var functions   = data[3].data;

        $scope.expressions  = expressions.expressions;

        for (var functionName in functions) {
            switch (functionName) {
                case 'event':
                    //for (var eventName in events) {
                    //    var parameters = [eventName].concat(events[eventName].parameters);
                    //    var eventParameterList = generateParameterList(parameters);
                    //
                    //    $scope.functions.push(functionName + '(' + eventParameterList + ')');
                    //}
                    break;
                case 'isTiming':
                    for (var cron in crons) {
                        $scope.functions.push(functionName + '("' + crons[cron].event.expression + '")');
                    }
                    break;
            }
            var parameterList = generateParameterList(functions[functionName]);
            $scope.functions.push(functionName + '(' + parameterList + ')');
        }
    });

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.crons[job.jobId]) {
            $scope.crons[job.jobId] = job;
        }
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
