
App.run(/*@ngInject*/ function($rootScope, $uibModal, Speech, UserManagementSettings, Flash) {
    var globalRecognition = window.webkitSpeechRecognition || window.speechRecognition;
    if (!globalRecognition) {
        return;
    }

    $rootScope.speechRecognition = {
        recognizing:        false,
        final_transcript:   '',
        interim_transcript: ''
    };

    $rootScope.speechRecognition.speechRecognition = function () {
        $rootScope.speechRecognition.recognizing = true;
        $rootScope.speechRecognition.final_transcript = '';

        /*
        document.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                console.debug('ENTER was pressed');
            }
        });
        */

        let recognition = new globalRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = "de-DE"; // todo set correct locale

        let modal = $uibModal.open({
            windowClass: "speechRecognitionModal",
            templateUrl: "/templates/modal/speechRecognition.html"
        });

        recognition.onerror = function(event) {
            console.error(event);
        };

        recognition.onresult = function(event) {
            $rootScope.speechRecognition.interim_transcript = '';
            $rootScope.speechRecognition.final_transcript = '';
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
