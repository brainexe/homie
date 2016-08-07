
App.filter('translateToken', /*@ngInject*/ function(_) {
    return function(value, token) {
        return _(token.format(value));
    };
});
