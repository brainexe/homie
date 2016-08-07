
App.service('Widget.display', /*@ngInject*/ function(Displays) {
    return {
        template: '/templates/widgets/display.html',
        render: function ($scope, widget) {
            Displays.getData().success(function(data) {
                $scope.display = data.screens[widget.displayId];
            });
        }
    };
});

