
App.filter('filterArray', function() {
    return function(items, needle) {
        if (!needle || !items) {
            return items;
        }

        needle = needle.toLowerCase();
        return items.filter(function(item) {
            return item.toString().toLowerCase().indexOf(needle) !== -1;
        });
    }
});
