
App.ng.controller('NewWidgetController', ['$scope', '$modalInstance', 'widgets', 'dashboard', function($scope, $modalInstance, widgets, dashboard) {
    $scope.widgets   = widgets;
    $scope.dashboard = dashboard;
    $scope.payload   = {};

    $scope.addWidget = function(dashboard, widget) {
        var payload = {
            type: widget.widgetId,
            dashboard_id: dashboard.dashboardId,
            payload: $scope.payload
        };

        $.post('/dashboard/add/', payload, function(data) {
            App.Dashboard.$scope.dashboard = data; // TODO
            App.Dashboard.$scope.$apply();
        });
        $modalInstance.close();
    };

    $scope.close = function() {
        $modalInstance.close();
    }
}]);
