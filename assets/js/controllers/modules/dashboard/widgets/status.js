
App.service('Widget.status', /*@ngInject*/ function($interval, Status) {
    function update($scope) {
        Status.getData().success(function(data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    }

    return {
        template: '/templates/widgets/status.html',
        render ($scope) {
            update($scope);

            var interval = $interval(function() {
                update($scope);
            }, 15000);

            $scope.$on('$destroy', function() {
                $interval.cancel(interval);
            });
        }
    };
});

