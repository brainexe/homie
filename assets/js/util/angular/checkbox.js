
App.directive('checkbox', /*@ngInject*/ function () {
    return {
        restrict: 'E',
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
        template: `<div class="checkbox-slider">
    <label>
        <input ng-model="ngModel" type="checkbox"><span>{{label}}&nbsp;</span>
    </label>
</div>`,
        scope: {
            ngModel: "=",
            ngChange: "&",
            label: "@",
        }
    };
});
