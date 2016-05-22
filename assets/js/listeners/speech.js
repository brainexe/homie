
App.service('Listener.Speech', ['$rootScope', 'Speech', function($rootScope, Speech) {
    if (!window.webkitSpeechRecognition) {
        return;
    }

    var recognition = new window.webkitSpeechRecognition();
    recognition.continuous = true;
    recognition.lang = "de-DE"; // TODO

    // recognition.interimResults = true;
    recognition.onresult = function(event) {
        var result = event.results[0][0];
        $rootScope.$broadcast('flash', [result.transcript, 'success']);
        Speech.sendText(result.transcript)
    };

    recognition.start();
}]);
