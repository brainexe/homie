
App.service('Widget.speech_recognition', /*@ngInject*/ function(Speech) {
    return {
        template: '/templates/widgets/speech_recognition.html',
        render ($scope) {
            $scope.text = '';

            $scope.submit = function(text) {
                $scope.text = '';
                Speech.sendText(text);
            };
        }
    };
});
