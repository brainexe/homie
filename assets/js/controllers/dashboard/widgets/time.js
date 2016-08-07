
App.service('Widget.time', /*@ngInject*/ function($interval) {
    return {
        template: '/templates/widgets/time.html',
        render: function ($scope, widget) {
            var interval = $interval(function() {
                $scope.time = Date.now();
            }, 1000);
            $scope.time = Date.now();

            $scope.$on('$destroy', function() {
                $interval.cancel(interval);
            });
        }
    }
});
