
App.controller('NewWidgetController', ['$scope', '$uibModalInstance', 'widgets', 'Dashboard', 'dashboards', 'currentDashboard', function($scope, $uibModalInstance, widgets, Dashboard, dashboards, currentDashboard) {
    $scope.widgets     = widgets;
    $scope.dashboards  = dashboards;
    $scope.payload     = {};
    $scope.dashboardId = currentDashboard && currentDashboard.dashboardId;

    $scope.addWidget = function(dashboardId, widget) {
        var payload = {
            type:        widget.widgetId,
            dashboardId: dashboardId,
            payload:     $scope.payload
        };

        Dashboard.add(payload).success(function(data) {
            $uibModalInstance.close(data);
        });
    };

    $scope.close = function() {
        $uibModalInstance.close();
    }
}]);
