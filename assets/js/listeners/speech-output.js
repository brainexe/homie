
App.service('Listener.SpeechOutput', ['$rootScope', 'UserManagement.Settings', function($rootScope, Settings) {
    if (!speechSynthesis) {
        return;
    }
    Settings.getAll().success(function(settings) {
        if (!settings.espeakBrowserOutput) {
            return;
        }
        $rootScope.$on('espeak.speak', function(eventName, event) {
            var utterance = new SpeechSynthesisUtterance(event.espeak.text);
            utterance.rate = 0.8;
            speechSynthesis.speak(utterance);
        });
    });
}]);
