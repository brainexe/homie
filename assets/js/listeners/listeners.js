
App.service('Listeners', ['$injector', function($injector) {
    return function () {
        $injector.get('Listener.BrowserSensorNotification');
    }
}]);
