
App.directive('checkbox', /*@ngInject*/ function () {
    return {
        restrict: 'EA',
        template: `
<div class="checkbox-slider">
    <label>
        <input ng-model="ngModel" type="checkbox"><span>&nbsp;</span>
    </label>
</div>`,
        scope: {
            ngModel: "="
        }
    };
});
