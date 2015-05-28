
App.ng.service('Widget.speak', ['Speak', '_', function(Speak, _) {
    return {
        title: _('Speak'),

        init: function ($scope) {
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
        },
        render: function ($scope, widget) {
        }
    }
}]);
