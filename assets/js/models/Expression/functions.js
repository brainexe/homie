
App.service('Expression.Functions', ['$q', 'Expression', 'MessageQueue', 'Sensor', 'Cache', function ($q, Expression, MessageQueue, Sensor, Cache) {
    // todo caching
    var cacheKey = 'expressionFunctions';

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
                    handleEvent(functionName, events);
                    break;
                case 'isSensorValue':
                case 'getSensorValue':
                    handleSensor(functionName, sensors);
                    break;
                case 'isTiming':
                    handleCrons(functionName, crons);
                    break;
            }
            add(functionName, functions[functionName]);
        }

        return allFunctions;
    });

    function add(functionName2, parameters, label) {
        var parameterList   = generateParameterList(parameters);
        var expression      = functionName2 + '(' + parameterList + ')';
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

    function handleEvent(functionName, events) {
        for (var eventName in events) {
            add(functionName, [eventName]);
        }
    }

    function handleCrons(functionName, crons) {
        for (var cron in crons) {
            add(functionName, [crons[cron].event.event.timingId], crons[cron].event.expression);
        }
    }

    function handleSensor(functionName, sensors) {
        for (var sensorIdx in sensors) {
            add(functionName, [sensors[sensorIdx].sensorId], sensors[sensorIdx].name);
        }
    }
}]);
