
App.filter('notEmpty', ['lodash', function (lodash) {
    return function (input) {
        return !lodash.isEmpty(input);
    };
}]);
