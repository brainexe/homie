
App.filter('filterArray', function() {
    return function(items, needle) {
        if (!needle || !items) {
            return items;
        }

        needle = needle.toLowerCase();
        return items.filter(item =>
            item.toString().toLowerCase().includes(needle)
        );
    };
});
