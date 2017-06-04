
App.run(/*@ngInject*/ function($rootScope, UserManagementSettings) {
    if (!window.speechSynthesis) {
        console.log("window.speechSynthesis is not supported!");
        return;
    }

    UserManagementSettings.getAll().then(function(result) {
        let settings = result.data;
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
