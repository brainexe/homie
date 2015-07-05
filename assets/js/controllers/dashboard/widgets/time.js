App.service('Widget.time', ['_', function(_) {
    return {
        render: function ($scope, widget) {
            window.setInterval(function() {
                $scope.time = new Date().toString();
            }, 1000);
        }
    }
}]);
