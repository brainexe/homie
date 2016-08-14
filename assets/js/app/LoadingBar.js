
App.config(/*@ngInject*/ (cfpLoadingBarProvider) => {
    cfpLoadingBarProvider.includeSpinner   = false;
    cfpLoadingBarProvider.latencyThreshold = 200;
});
