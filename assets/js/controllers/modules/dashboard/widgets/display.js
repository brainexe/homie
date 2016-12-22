
App.service('Widget.display', /*@ngInject*/ function(Displays) {
    return {
        template: '/templates/widgets/display.html',
        render ($scope, widget) {
            Displays.getData().then(function(data) {
                $scope.display = data.data.screens[widget.displayId];
            });
        }
    };
});

