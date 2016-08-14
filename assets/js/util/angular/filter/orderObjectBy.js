
App.filter('orderObjectBy', /*@ngInject*/ function(lodash) {
    return function(input, attribute) {
        var order = 'asc';
        if (attribute.charAt(0) === '-') {
            attribute = attribute.substring(1);
            order = 'desc';
        }

        return lodash.orderBy(input, [attribute], [order]);
    };
});
