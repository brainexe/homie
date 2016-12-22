
App.directive('debug',  /*@ngInject*/ function (Config) {
    return {
        restrict: 'A',
        link ($scope, element) {
            Config.getAll().then(function(config) {
                if (!config.data.debug) {
                    element.replaceWith('');
                }
            });
        }
    };
});
