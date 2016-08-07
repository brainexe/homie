
App.filter('notEmpty', /*@ngInject*/ function (lodash) {
    return function (input) {
        return !lodash.isEmpty(input);
    };
});
