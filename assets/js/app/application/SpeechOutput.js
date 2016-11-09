
App.run(/*@ngInject*/ function($rootScope, UserManagementSettings) {
    if (!window.speechSynthesis) {
        return;
    }

    UserManagementSettings.getAll().success(function(settings) {
        if (!settings.espeakBrowserOutput) {
            return;
        }
        $rootScope.$on('espeak.speak', function(eventName, event) {
            var utterance = new SpeechSynthesisUtterance(event.espeak.text);

            if (event.espeak.speaker) {
                utterance.lang = event.espeak.speaker;
            }
            utterance.rate = 0.8;
            speechSynthesis.speak(utterance);
        });
    });
});
