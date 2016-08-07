
App.directive('contentItem', /*@ngInject*/ function ($compile, TemplateLoader, WidgetFactory) {
    var linker = function($scope, element) {
        var widget = $scope.widget;
        var renderer = WidgetFactory(widget.type);
        var template = TemplateLoader(renderer.template);

        $scope.setTitle = function (title) {
            $scope.$parent.title = title;
        };

        template.success(function(html) {
            element[0].innerHTML = html;
            $compile(element.contents())($scope);

            renderer.render($scope, widget, element);
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
});

