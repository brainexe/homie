
App.service('Expression.Functions', ['$q', 'Expression', 'MessageQueue', 'Sensor', 'Cache', function ($q, Expression, MessageQueue, Sensor, Cache) {
    var cacheKey = 'expressionFunctions';
    var allFunctions = [];

    if (Cache.get(cacheKey)) {
        return $q(function(resolve) {
            resolve(Cache.get(cacheKey));
        });
    }

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

        Cache.put(cacheKey, allFunctions);

        return allFunctions;
    });

    function add(functionName2, functionData, label) {
        var parameterList   = generateParameterList(functionData.parameters);
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
            add(functionName, {parameters: [eventName]});
        }
    }

    function handleCrons(functionName, crons) {
        for (var cron in crons) {
            add(functionName, {parameters: [crons[cron].event.event.timingId]}, crons[cron].event.expression);
        }
    }

    function handleSensor(functionName, sensors) {
        for (var sensorIdx in sensors) {
            add(functionName, {parameters: [sensors[sensorIdx].sensorId]}, sensors[sensorIdx].name);
        }
    }
}]);
