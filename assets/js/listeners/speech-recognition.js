
App.service('Listener.SpeechRecognition', ['$rootScope', 'Speech', 'UserManagement.Settings', function($rootScope, Speech, Settings) {
    if (!window.webkitSpeechRecognition) {
        return;
    }

    Settings.getAll().success(function(settings) {
        if (!settings.voiceControl) {
            return;
        }
        var recognition = new window.webkitSpeechRecognition();

        recognition.continuous = true;
        recognition.lang = "de-DE"; // TODO

        // recognition.interimResults = true;
        recognition.onresult = function(event) {
            var result = event.results[event.resultIndex][0];

            $rootScope.$broadcast('flash', [result.transcript, 'success']);

            Speech.sendText(result.transcript)
        };

        recognition.start();
    });
}]);
