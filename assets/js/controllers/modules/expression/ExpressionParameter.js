
App.directive('expressionParameter', function() {
    return {
        templateUrl: '/templates/expression/parameter.html',
        restrict: "E",
        scope: {
            parameter: '=',
            functions: '=',
            uiSelectParameter: '='
        },
        link ($scope) {
            $scope.uiSelectParameter = $scope.parameter;

            $scope.onUiSelect = function(selected) {
                $scope.parameter = selected.expression;
            };

            $scope.$watch(function() {
                return $scope.parameter;
            }, function(newVal) {
                $scope.uiSelectParameter = '';
            });
        }
    };
});
