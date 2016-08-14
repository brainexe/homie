
App.filter('translateToken', /*@ngInject*/ function(_) {
    return (value, token) =>
        _(token.format(value));
});
