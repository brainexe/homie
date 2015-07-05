
App.service('Widget.switch', ['Radios', function(Radios) {
    return {
        render: function ($scope, widget) {
            $scope.switches = [];

            $scope.setStatus = function(radio, status) {
                Radios.setRadio(radio.radioId, status);
            };

            Radios.getDataCached().success(function(switches) {
               for (var i in widget.switchIds) {
                   var currentSwitch = switches.radios[widget.switchIds[i]];
                   if (currentSwitch) {
                       $scope.switches.push(currentSwitch);
                   }
               }
            });
        }
    };
}]);

