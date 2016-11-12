
App.config(['$compileProvider', /*@ngInject*/ function ($compileProvider) {
    $compileProvider.debugInfoEnabled(DEBUG);

    // todo enable when available in angular
    // $compileProvider.commentDirectivesEnabled(false);
    // $compileProvider.cssClassDirectivesEnabled(false);
}]);
