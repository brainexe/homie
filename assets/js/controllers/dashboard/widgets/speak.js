
App.Widgets = App.Widgets || {}; // @TODO

App.Widgets.speak = {
    title: gettext('Speak'),

    init: function($scope) {
        $scope.pending = false;
        $scope.speak = function(text) {
            $scope.pending = true;
            text = text || prompt('Text?');
            var payload = {
                text: text
            };

            App.Speak.speak(payload).then(function() {
                $scope.text = '';
                $scope.pending = false;
                $scope.$apply();
            });
        };
    },
    render: function ($scope, widget) {
        $scope.$apply();
    }
};
