
App.Widgets.egg_timer = {
    title: gettext('Egg Timer'),

    init: function($scope) {
        $scope.times = [
            '2m',
            '5m',
            '10m',
            '20m',
            '30m'
        ];

        $scope.start = function(time) {
            App.EggTimer.setTimer(time);
        };

        $scope.prompt = function() {
            var time = prompt(gettext('Set Time'));
            App.EggTimer.setTimer(time);
        }
    },
    render: function ($scope, widget) {
        $scope.$apply();
    }
};
