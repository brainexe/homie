
App.controller('WidgetController', /*@ngInject*/ function ($scope, Dashboard) {
    var widgetPayload  = $scope.$parent.widget;

    Dashboard.getCachedMetadata().success(function(data) {
        var metadata = data.widgets[widgetPayload.type];

        $scope.title  = widgetPayload.title || metadata.name || widgetPayload.name;
        $scope.widget = widgetPayload;
    });
});
