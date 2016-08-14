
App.filter('notEmpty', /*@ngInject*/ (lodash) =>
    (input) => !lodash.isEmpty(input)
);
