
App.service('Widget.status', /*@ngInject*/ function($interval, Status) {
    function update($scope) {
        Status.getData().then(function(data) {
            $scope.stats = data.data.stats;
            $scope.jobs  = data.data.jobs;
            $scope.redis = data.data.redis;
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

