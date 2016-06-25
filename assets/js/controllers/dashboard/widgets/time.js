App.service('Widget.time', ['$interval', function($interval) {
    return {
        template: '/templates/widgets/time.html',
        render: function ($scope, widget) {
            $interval(function() {
                $scope.time = Date.now();
            }, 1000);
            $scope.time = Date.now();
        }
    }
}]);
