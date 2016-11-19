
App.service('OrderByMixin', /*@ngInject*/ function () {
    return {
        setOrderBy: function (key) {
            if (this.orderBy === key) {
                key = '-' + key;
            }

            this.orderBy = key;
        }
    };
});
