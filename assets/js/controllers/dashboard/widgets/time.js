App.service('Widget.time', ['$interval', function($interval) {
    return {
        render: function ($scope, widget) {
            $interval(function() {
                $scope.time = Date.now();
            }, 1000);
            $scope.time = Date.now();
        }
    }
}]);
