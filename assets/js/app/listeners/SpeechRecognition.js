
App.run(/*@ngInject*/ function(Speech, UserManagementSettings, Flash) {
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
        recognition.onresult = (event) => {
            var result = event.results[event.resultIndex][0];

            Speech.sendText(result.transcript);

            Flash.addFlash(result.transcript, Flash.SUCCESS);
        };

        recognition.start();
    });
});
