
App.service('Expression.Functions', ['$q', 'Expression', 'MessageQueue', 'Sensor', 'Cache', function ($q, Expression, MessageQueue, Sensor, Cache) {
    var allFunctions = [];

    return $q.all([
        MessageQueue.getJobs('message_queue.cron'),
        Expression.getEvents(),
        Expression.getFunctions(),
        Sensor.getCachedData()
    ]).then(function(data) {
        var crons       = data[0].data;
        var events      = data[1].data;
        var functions   = data[2].data;
        var sensors     = data[3].data.sensors;

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
                case 'isTiming':
                    for (var cron in crons) {
                        add(functionName, [crons[cron].event.event.timingId], crons[cron].event.expression);
                    }
                    break;
            }
            add(functionName, functions[functionName]);
        }

        return allFunctions;
    });

    function add(functionName2, parameters, label) {
        var parameterList = generateParameterList(parameters);
        var expression = functionName2 + '(' + parameterList + ')';
        var expressionLabel = expression;

        if (label) {
            expressionLabel += ' # ' + label;
        }

        allFunctions.push({
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
}]);
