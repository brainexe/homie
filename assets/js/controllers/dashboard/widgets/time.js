
App.Widgets.time = {
    interval: 1000,
    title: gettext('Current Time'),
    render: function ($scope, widget) {
        $scope.time = new Date().toString();
    }
};
