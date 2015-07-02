
App.service('Widget.status', ['Status', '_', function(Status, _) {
    function update($scope) {
        Status.getData().success(function (data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    }

    return {
        title: _('Status'),

        render: function ($scope, widget) {
            update($scope);

            window.setInterval(function() {
                update($scope);
            }, 15000);
        }
    };
}]);

