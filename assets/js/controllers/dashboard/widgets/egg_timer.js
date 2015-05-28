
App.ng.service('Widget.egg_timer', ['EggTimer', '_', function(EggTimer, _) {
    return {
        title: _('Egg Timer'),

        init: function($scope) {
            $scope.times = [
                '2m',
                '5m',
                '10m',
                '20m',
                '30m'
            ];

            $scope.start = function(time) {
                EggTimer.setTimer(time);
            };

            $scope.prompt = function() {
                var time = prompt(_('Set Time'));
                EggTimer.setTimer(time);
            }
        },
        render: function ($scope, widget) {
        }
    };
}]);
