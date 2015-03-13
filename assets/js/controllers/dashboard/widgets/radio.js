
App.Widgets.radio = {
    render: function ($scope, widget) {
        var radios = App.Radios.loadAll();
        radios.then(function(radios) {
            var radio = radios[widget.radioId];
            if (radio) {
                $scope.setTitle(radio.name);
            }

            $scope.setStatus = function(radio, status) {
                App.Radios.setRadio(radio.radioId, status);
            };

            $scope.radio = radio;
            $scope.$apply();
        });
    }
};
