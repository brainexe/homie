
App.directive('expressionParameter', [function() {
    return {
        templateUrl: '/templates/expression/parameter.html',
        restrict: "E",
        scope: {
            parameter: '=',
            functions: '='
        }
    };
}]);
