
App.config(['$compileProvider', '$locationProvider', /*@ngInject*/ function ($compileProvider, $locationProvider) {
    $locationProvider.hashPrefix('');

    $compileProvider.debugInfoEnabled(DEBUG);
    $compileProvider.commentDirectivesEnabled(false);
    $compileProvider.cssClassDirectivesEnabled(false);
    $compileProvider.preAssignBindingsEnabled(true);
}]);
