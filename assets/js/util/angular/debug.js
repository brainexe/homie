
App.directive('debug', ['Config', function (Config) {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs) {
            Config.get('debug').then(function(config) {
                if (!config[0]) {
                    element.replaceWith('');
                }
            });
        }
    }
}]);
