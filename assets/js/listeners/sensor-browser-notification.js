
App.service('Listener.BrowserSensorNotification', ['$rootScope', 'BrowserNotification', function($rootScope, BrowserNotification) {
    var sensorValues = {};
    $rootScope.$on('sensor.value', function (eventName, event) {
        var old = sensorValues[event.sensorVo.sensorId];
        var text;
        if (old != event.valueFormatted) {
            sensorValues[event.sensorVo.sensorId] = event.valueFormatted;
            if (old) {
                text = '{0}: {1} -> {2}'.format(event.sensorVo.name, old, event.valueFormatted);
            } else {
                text = '{0}: {1}'.format(event.sensorVo.name, event.valueFormatted);
            }
            BrowserNotification.show(text);
        }
    });
}]);
