
App.filter('translateToken', ['_', function(_) {
    return function(value, token) {
        return _(token.format(value));
    };
}]);
