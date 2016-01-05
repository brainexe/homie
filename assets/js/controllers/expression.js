
App.controller('ExpressionController', ['$scope', '$rootScope', 'Expression', 'MessageQueue', function ($scope, $rootScope, Expression, MessageQueue) {
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

    // todo improve chain
    $scope.reloadCrons().success(function(crons) {
        Expression.getData().success(function(expressions) {
            Expression.getEvents().success(function(events) {
                Expression.getFunctions().success(function(functions) {
                    $scope.expressions   = expressions.expressions;

                    for (var functionName in functions) {
                        switch (functionName) {
                            case 'event':
                                for (var eventName in events) {
                                    var parameters = [eventName].concat(events[eventName].parameters);
                                    var eventParameterList = generateParameterList(parameters);

                                    $scope.functions.push(functionName + '(' + eventParameterList + ')');
                                }
                                break;
                            case 'isTiming':
                                for (var cron in crons) {
                                    $scope.functions.push(functionName + '("' + crons[cron].event.expression + '")');
                                }
                        }
                        var parameterList = generateParameterList(functions[functionName]);
                        $scope.functions.push(functionName + '(' + parameterList + ')');
                    }
                });
            });
        });
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
            alert(result); // todo nice UI
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
