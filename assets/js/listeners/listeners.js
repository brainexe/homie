
App.service('Listeners', ['$injector', function($injector) {
    return function () {
        $injector.get('Listener.BrowserSensorNotification');
        $injector.get('Listener.SpeechRecognition');
        $injector.get('Listener.SpeechOutput');
    }
}]);
