
App.service('WidgetFactory', ['$injector',
    function($injector) {
        return function(type) {
            return $injector.get('Widget.' + type)
        };
    }
]);
