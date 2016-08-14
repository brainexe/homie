
App.service('WidgetFactory', /*@ngInject*/ function($injector) {
    return (type) => $injector.get('Widget.' + type);
});
