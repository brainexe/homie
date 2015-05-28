
App.ng.directive('contentItem', ['$compile', 'TemplateLoader', 'WidgetFactory', function ($compile, TemplateLoader, WidgetFactory) {
    var linker = function(scope, element) {
        var widget = scope.widget;

        scope.setTitle = function (title) {
            scope.$parent.title = title;
        };

        var template = TemplateLoader('/templates/widgets/' + widget.type + '.html');

        template.success(function(html) {
            // set template first
            element.html(html).show();
            $compile(element.contents())(scope);

            // init...
            var renderer = WidgetFactory(widget.type);
            if (renderer.init) {
                renderer.init(scope, widget)
            }

            // update...
            function update() {
                renderer.render(scope, widget);
            }
            update();

            if (renderer.interval) {
                window.setInterval(function() {
                    update();
                    scope.$apply();
                }, renderer.interval);
            }
        });
    };

    return {
        restrict: "E",
        link: linker,
        scope: {
            template: '=',
            widget:   '=',
            type:     '='
        }
    };
}]);

App.ng.controller('WidgetController', ['$scope', 'WidgetFactory', function ($scope, WidgetFactory) {
    var widgetPayload = $scope.$parent.widget;
    var widgetMeta    = WidgetFactory(widgetPayload.type);

    // todo use name from widgets definition
    $scope.title  = widgetMeta.title || widgetPayload.name;
    $scope.widget = widgetPayload;
}]);
