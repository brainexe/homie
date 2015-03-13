
App.ng.directive('contentItem', function ($compile) {
    var templates = new App.TemplateLoader();

    var linker = function(scope, element) {
        var widget = scope.widget;

        scope.setTitle = function (title) {
            scope.$parent.title = title;
            scope.$parent.$apply();
        };
        var template = templates.load('/templates/widgets/' + widget.type + '.html');
        template.then(function(html) {
            // set template first
            element.html(html).show();
            $compile(element.contents())(scope);

            // init...
            var renderer = App.Widgets[widget.type];
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

            scope.$apply();
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

App.ng.controller('WidgetController', ['$scope', function ($scope) {
    var widget_payload = $scope.$parent.widget;
    var widget_meta    = App.Widgets[widget_payload.type];

    // todo use name from widgets definition
    $scope.title  = widget_meta.title || widget_payload.name;
    $scope.widget = widget_payload;
}]);
