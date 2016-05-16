
App.directive('debug', ['Config', function (Config) {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs) {
            Config.getAll().success(function(config) {
                if (!config.debug) {
                    element.replaceWith('');
                }
            });
        }
    }
}]);
