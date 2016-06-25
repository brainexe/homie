
App.service('Widget.switch', ['Switches', function(Switches) {
    return {
        template: '/templates/widgets/radio.html',
        render: function ($scope, widget) {
            $scope.switches = [];

            $scope.setStatus = function(switchVO, status) {
                Switches.setStatus(switchVO.switchId, status);
            };

            Switches.getDataCached().success(function(switches) {
                for (var i in widget.switchIds) {
                    var currentSwitch = switches.switches[widget.switchIds[i]];
                    if (currentSwitch) {
                        $scope.switches.push(currentSwitch);
                    }
                }
            });
        }
    };
}]);

