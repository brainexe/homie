
App.config(/*@ngInject*/ function (cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeSpinner   = false;
    cfpLoadingBarProvider.latencyThreshold = 200;
});
