
App.config(['$compileProvider', /*@ngInject*/ function ($compileProvider) {
    $compileProvider.debugInfoEnabled(!DEBUG);

    // todo enable when
    // $compileProvider.commentDirectivesEnabled(false);
    // $compileProvider.cssClassDirectivesEnabled(false);
}]);
