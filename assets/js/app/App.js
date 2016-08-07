
var App = angular.module('homie', [
    'ang-drag-drop',
    'ngRoute',
    'ngSanitize',
    'angular-cache',
    'ui.bootstrap',
    'ui.select',
    'as.sortable',
    'colorpicker.module',
    'gettext',
    'angular-loading-bar'
]);
App.run(
    /*@ngInject*/
    function ($rootScope) {
        // TODO refactor
        $rootScope.prompt = prompt.bind(window);
    }
);
