
App.run(/*@ngInject*/ function($rootScope, Speech, UserManagementSettings) {
    if (!window.webkitSpeechRecognition) {
        return;
    }

    UserManagementSettings.getAll().success(function(settings) {
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
});
