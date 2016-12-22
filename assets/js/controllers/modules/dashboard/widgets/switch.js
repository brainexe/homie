
App.service('Widget.switch', /*@ngInject*/ function(Switches, lodash) {
    return {
        template: '/templates/widgets/switch.html',
        render ($scope, widget) {
            $scope.switches = [];

            $scope.setStatus = function(switchVO, status) {
                Switches.setStatus(switchVO.switchId, status);
            };

            Switches.getDataCached().then(function(switches) {
                $scope.switches = lodash.map(widget.switchIds, function(switchId) {
                    return switches.data.switches[switchId];
                });
            });
        }
    };
});

