
App.directive('checkbox', /*@ngInject*/ function () {
    return {
        restrict: 'E',
        templateUrl: '/templates/directives/checkbox.html',
        replace: true,
        link: function($scope) {
            if ($scope.ngChange) {
                $scope.$watch(function() { return $scope.ngModel}, function(newValue, oldValue) {
                    if (newValue !== oldValue) {
                        $scope.ngChange();
                    }
                });
            }
        },
        scope: {
            ngModel: "=",
            ngChange: "&",
            label: "@",
        }
    };
});
