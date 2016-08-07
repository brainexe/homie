
App.service('WidgetFactory', /*@ngInject*/ function($injector) {
    return function(type) {
        return $injector.get('Widget.' + type)
    };
});
