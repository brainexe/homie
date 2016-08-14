
App.service('Widget.time', /*@ngInject*/ function($interval) {
    return {
        template: '/templates/widgets/time.html',
        render ($scope) {
            // todo use now time
            var interval = $interval(function() {
                $scope.time = Date.now();
            }, 1000);
            $scope.time = Date.now();

            $scope.$on('$destroy', function() {
                $interval.cancel(interval);
            });
        }
    };
});
