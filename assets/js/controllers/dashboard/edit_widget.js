
App.controller('EditWidgetController', /*@ngInject*/ function($scope, $uibModalInstance, Dashboard, dashboardId, widget) {
    $scope.payload = widget;

    Dashboard.getCachedMetadata().success(function(data) {
        $scope.widget = data.widgets[widget.type];
    });

    $scope.save = function() {
        Dashboard.updateWidget(dashboardId, widget).success(function(data) {
            $uibModalInstance.close(data);
        });
    };

    $scope.close = function() {
        $uibModalInstance.close();
    }
});
