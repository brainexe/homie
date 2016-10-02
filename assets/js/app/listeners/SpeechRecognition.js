
App.run(/*@ngInject*/ function($rootScope, $uibModal, Speech, UserManagementSettings, Flash) {
    if (!window.webkitSpeechRecognition) {
        return;
    }

    function startTriggerProcess() {
        var trigger = new window.webkitSpeechRecognition();
        trigger.lang = "de-DE"; // todo set correct loale
        trigger.onresult = function(event) {
            var result = event.results[0][0].transcript;
            console.log(event);
            console.log(event.results[0][0].transcript);
            if (result.indexOf('Start') > -1) {
                console.log('YES');
                trigger.onend = null;
                trigger.stop();
            }
        };

        trigger.onend = function () {
            trigger.start();
        };

        var grammar = '#JSGF V1.0; public <start> = Start | Homie;';
        var speechRecognitionList = new webkitSpeechGrammarList();
        speechRecognitionList.addFromString(grammar, 1);
        trigger.grammars = speechRecognitionList;
        trigger.interimResults = true;
        trigger.continuous = true;
        trigger.maxAlternatives = 1;

        trigger.start();
    }

    $rootScope.speechRecognition = {
        recognizing:        false,
        final_transcript:   '',
        interim_transcript: ''
    };

    $rootScope.speechRecognition.speechRecognition = function () {
        $rootScope.speechRecognition.recognizing = true;
        $rootScope.speechRecognition.final_transcript = '';

        var recognition = new window.webkitSpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = "de-DE"; // todo set correct loale

        var modal = $uibModal.open({
            windowClass: "speechRecognitionModal",
            templateUrl: "/templates/modal/speechRecognition.html"
        });

        recognition.onerror = function(event) {
            console.error(event);
        };

        recognition.onresult = function(event) {
            $rootScope.speechRecognition.interim_transcript = '';
            if (typeof event.results === 'undefined') {
                recognition.stop();
                return;
            }
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    $rootScope.speechRecognition.final_transcript += event.results[i][0].transcript;
                } else {
                    $rootScope.speechRecognition.interim_transcript += event.results[i][0].transcript;
                }
            }

            if ($rootScope.speechRecognition.final_transcript) {
                recognition.stop();

                Speech.sendText($rootScope.speechRecognition.final_transcript);
                Flash.addFlash($rootScope.speechRecognition.final_transcript, Flash.SUCCESS);
                $rootScope.speechRecognition.recognizing = false;
                modal.close();
            }
            $rootScope.$apply();
        };

        recognition.start();
    };
});
