
App.ng.controller('NewWidgetController', ['$scope', '$modalInstance', 'widgets', 'dashboards', function($scope, $modalInstance, widgets, dashboards) {
    $scope.widgets   = widgets;
    $scope.dashboards = dashboards;
    $scope.payload   = {};

    $scope.addWidget = function(dashboardId, widget) {
        console.log(arguments);

        var payload = {
            type:         widget.widgetId,
            dashboard_id: dashboardId,
            payload:      $scope.payload
        };

        $.post('/dashboard/add/', payload, function(data) {
            App.Dashboard.$scope.dashboards[data.dashboardId] = data;
            App.Dashboard.$scope.$apply();
        });
        $modalInstance.close();
    };

    $scope.close = function() {
        $modalInstance.close();
    }
}]);
