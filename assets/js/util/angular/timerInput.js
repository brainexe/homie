
App.directive('timerInput', /*@ngInject*/ function () {
    // todo add datepicker
    return {
        restrict: 'EA',
        template: `
    <input
        ng-model="ngModel"
        ng-enter="ngEnter"
        type="text"
        class="form-control"
        placeholder="15m"
    />
    <span class="help-block" translate>e.g. 10s, 30m, 1h</span>
`,
        scope: {
            ngModel: "=",
            ngEnter: "&",
        }
    };
});

