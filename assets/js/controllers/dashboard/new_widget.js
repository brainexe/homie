
App.controller('NewWidgetController', ['$scope', '$modalInstance', 'widgets', 'Dashboard', 'dashboards', function($scope, $modalInstance, widgets, Dashboard, dashboards) {
    $scope.widgets    = widgets;
    $scope.dashboards = dashboards;
    $scope.payload    = {};

    $scope.addWidget = function(dashboardId, widget) {
        var payload = {
            type:        widget.widgetId,
            dashboardId: dashboardId,
            payload:     $scope.payload
        };

        Dashboard.add(payload).success(function(data) {
            $scope.$parent.dashboards[data.dashboardId] = data;
        });
        $modalInstance.close();
    };

    $scope.close = function() {
        $modalInstance.close();
    }
}]);
