
App.controller('EditWidgetController', /*@ngInject*/ function($scope, $uibModalInstance, Dashboard, dashboardId, widget) {
    $scope.payload = widget;

    Dashboard.getCachedMetadata().then(function(data) {
        $scope.widget = data.data.widgets[widget.type];
    });

    $scope.save = function() {
        Dashboard.updateWidget(dashboardId, widget).then(function(data) {
            $uibModalInstance.close(data.data);
        });
    };

    $scope.close = function() {
        $uibModalInstance.close();
    };
});
