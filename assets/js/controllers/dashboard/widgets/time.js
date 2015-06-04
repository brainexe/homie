App.service('Widget.time', ['_', function(_) {
    return {
        interval: 1000,
        title: _('Current Time'),
        render: function ($scope, widget) {
            $scope.time = new Date().toString();
        }
    }
}]);
