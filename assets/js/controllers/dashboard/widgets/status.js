
App.service('Widget.status', ['$interval', 'Status', '_', function($interval, Status, _) {
    function update($scope) {
        Status.getData().success(function(data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    }

    return {
        template: '/templates/widgets/status.html',
        render: function ($scope, widget) {
            update($scope);

            var interval = $interval(function() {
                update($scope);
            }, 15000);

            $scope.$on('$destroy', function() {
                $interval.cancel(interval);
            });
        }
    };
}]);

