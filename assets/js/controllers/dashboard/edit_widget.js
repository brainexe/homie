
App.controller('EditWidgetController', ['$scope', '$modalInstance', 'Dashboard', 'dashboardId', 'widget', function($scope, $modalInstance, Dashboard, dashboardId, widget) {
    $scope.payload = widget;

    Dashboard.getCachedMetadata().success(function(data) {
        $scope.widget = data.widgets[widget.type];
    });

    $scope.save = function() {
        Dashboard.updateWidget(dashboardId, widget).success(function(data) {
            $modalInstance.close(data);
        });
    };

    $scope.close = function() {
        $modalInstance.close();
    }
}]);
