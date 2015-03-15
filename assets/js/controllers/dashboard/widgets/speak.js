
App.Widgets.speak = {
    title: _('Speak'),

    init: function($scope) {
        $scope.speak = function(text) {
            text = text || prompt('Text?');
            var payload = {
                text: text
            };

            App.Speak.speak(payload);
        };
    },
    render: function ($scope, widget) {
        $scope.$apply();
    }
};
