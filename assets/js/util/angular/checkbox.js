
App.directive('checkbox', /*@ngInject*/ function () {
    return {
        restrict: 'EA',
        link: function($scope, element, attrs) {
            if ($scope.ngChange) {
                $scope.$watch(function() { return $scope.ngModel}, function(newValue, oldValue) {
                    if (newValue != oldValue) {
                        console.log(newValue, oldValue)
                        $scope.ngChange();
                    }
                });
            }
        },
        template: `
<div class="checkbox-slider">
    <label>
        <input ng-model="ngModel" type="checkbox"><span>&nbsp;</span>
    </label>
</div>`,
        scope: {
            ngModel: "=",
            ngChange: "&",
        }
    };
});
