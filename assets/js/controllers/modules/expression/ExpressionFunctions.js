
App.service('ExpressionFunctions', /*@ngInject*/ function ($q, Expression, MessageQueue, Sensor, Cache) {
    var cacheKey = 'expressionFunctions',
      allFunctions = {
        actions: [],
        triggers: []
    };

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
        var functionName;

        for (functionName in functions) {
            switch (functionName) {
                case 'isEvent':
                    handleIsEvent(functionName, events);
                    break;
                case 'event':
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

        var data = {
            label: expressionLabel,
            expression
        };

        if (functionData.isTrigger) {
            allFunctions.triggers.push(data);
        }
        if (functionData.isAction) {
            allFunctions.actions.push(data);
        }
    }

    function generateParameterList(array) {
        var parameterList = [];
        array.forEach(function(parameter) {
            if (typeof parameter === 'object') {
                parameterList.push('"' + parameter.name + '"');
            } else {
                parameterList.push('"' + parameter + '"');
            }
        });
        return parameterList.join(', ');
    }

    function handleIsEvent(functionName, events) {
        for (var eventName in events) {
            add(functionName, {
                parameters: [eventName],
                isTrigger: true,
                isAction: false
            });
        }
    }

    function handleEvent(functionName, events) {
        for (var eventName in events) {
            add(functionName, {
                parameters: [eventName],
                isTrigger: false,
                isAction: true
            });
        }
    }

    function handleCrons(functionName, crons) {
        for (var cron in crons) {
            add(functionName, {
                parameters: [crons[cron].event.event.timingId],
                isTrigger: true,
                isAction: false
            }, crons[cron].event.expression);
        }
    }

    function handleSensor(functionName, sensors) {
        for (var sensor of sensors) {
            add(functionName, {
                parameters: [sensor.sensorId],
                isTrigger: true,
                isAction: true
            }, sensor.name);
        }
    }
});
