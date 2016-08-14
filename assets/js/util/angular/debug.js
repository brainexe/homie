
App.directive('debug',  /*@ngInject*/ function (Config) {
    return {
        restrict: 'A',
        link ($scope, element) {
            Config.getAll().success(function(config) {
                if (!config.debug) {
                    element.replaceWith('');
                }
            });
        }
    };
});
