
App.service('Widget.speak', ['Speak', function(Speak) {
    return {
        render: function ($scope) {
            $scope.pending = false;
            $scope.speak = function (text) {
                $scope.pending = true;
                text = text || prompt('Text?');
                var payload = {
                    text: text
                };

                Speak.speak(payload).success(function () {
                    $scope.text    = '';
                    $scope.pending = false;
                });
            };
        }
    }
}]);
