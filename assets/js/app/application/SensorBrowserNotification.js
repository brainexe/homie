
App.run(/*@ngInject*/ function($rootScope, BrowserNotification) {
    var sensorValues = {};
    $rootScope.$on('sensor.value', function (eventName, event) {
        var old = sensorValues[event.sensorVo.sensorId];
        var text;
        if (old !== event.valueFormatted) {
            sensorValues[event.sensorVo.sensorId] = event.valueFormatted;
            if (old) {
                text = `${event.sensorVo.name}: ${old} -> ${event.valueFormatted}`;
            } else {
                text = `${event.sensorVo.name}: ${event.valueFormatted}`;
            }

            BrowserNotification.show(text);
        }
    });
});
