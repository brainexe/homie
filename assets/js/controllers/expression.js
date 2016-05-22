
App.controller('ExpressionController', ['$scope', '$rootScope', '$q', 'Expression', 'MessageQueue', 'Sensor', 'Cache', function ($scope, $rootScope, $q, Expression, MessageQueue, Sensor, Cache) {
	$scope.expressions    = {};
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

    $scope.reloadCrons = function() {
        return MessageQueue.getJobs('message_queue.cron').success(function(data) {
            $scope.crons = data;
        });
    };

    // todo cache + outsource
    $q.all([
        $scope.reloadCrons(),
        Expression.getData(),
        Expression.getEvents(),
        Expression.getFunctions(),
        Sensor.getCachedData()
    ]).then(function(data) {
        var crons       = data[0].data;
        var expressions = data[1].data;
        var events      = data[2].data;
        var functions   = data[3].data;
        var sensors     = data[4].data.sensors;

        $scope.expressions  = expressions.expressions;

        function add(functionName2, parameters, label) {
            var parameterList = generateParameterList(parameters);
            var expression = functionName2 + '(' + parameterList + ')';
            var expressionLabel = expression;

            if (label) {
                expressionLabel += ' # ' + label;
            }

            $scope.functions.push({
                label: expressionLabel,
                expression: expression
            });
        }

        function generateParameterList(array) {
            var parameterList = [];
            array.forEach(function(parameter) {
                if (typeof parameter == 'object') {
                    parameterList.push('"' + parameter.name + '"');
                } else {
                    parameterList.push('"' + parameter + '"');
                }
            });
            return parameterList.join(', ');
        }

        for (var functionName in functions) {
            switch (functionName) {
                case 'isEvent':
                    for (var eventName in events) {
                        add(functionName, [eventName]);
                    }
                    break;
                case 'isSensorValue':
                case 'getSensorValue':
                    for (var sensorIdx in sensors) {
                        add(functionName, [sensors[sensorIdx].sensorId], sensors[sensorIdx].name);
                    }
                    break;
                case 'event':
                    //for (var eventName in events) {
                    //    var parameters = [eventName].concat(events[eventName].parameters);
                    //    add(functionName, parameters);
                    //}
                    break;
                case 'isTiming':
                    for (var cron in crons) {
                        add(functionName, [crons[cron].event.event.timingId], crons[cron].event.expression);
                    }
                    break;
            }
            add(functionName, functions[functionName]);
        }
    });

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
