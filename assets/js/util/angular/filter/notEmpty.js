App.filter('notEmpty', function () {
    return function (input) {
        if (!input) {
            return false;
        }

        if (Array.isArray(input)) {
            return input.length;
        }

        return Object.keys(input).length > 0;
    };
});
