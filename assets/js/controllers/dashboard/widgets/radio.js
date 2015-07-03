
App.service('Widget.radio', ['Radios', function(Radios) {
    return {
        render: function ($scope, widget) {
           Radios.getDataCached().success(function(radios) {
               // todo pass multiple switches
                var radio = radios.radios[widget.radioId];
                if (radio) {
                    $scope.setTitle(radio.name);
                }

                $scope.setStatus = function(radio, status) {
                    Radios.setRadio(radio.radioId, status);
                };

                $scope.radio = radio;
            });
        }
    };
}]);

