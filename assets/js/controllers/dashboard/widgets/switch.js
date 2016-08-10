
App.service('Widget.switch', /*@ngInject*/ function(Switches, lodash) {
    return {
        template: '/templates/widgets/switch.html',
        render: function ($scope, widget) {
            $scope.switches = [];

            $scope.setStatus = function(switchVO, status) {
                Switches.setStatus(switchVO.switchId, status);
            };

            Switches.getDataCached().success(function(switches) {
                $scope.switches = lodash.map(widget.switchIds, function(switchId) {
                    return switches.switches[switchId];
                });
            });
        }
    };
});

