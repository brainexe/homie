
App.filter('filterArray', function() {
    return function(items, needle) {
        if (!needle || !items) {
            return items;
        }

        var out = [];
        needle = needle.toLowerCase();
        items.forEach(function(item) {
            if (item.toString().toLowerCase().indexOf(needle) !== -1) {
                out.push(item);
            }
        });

        return out;
    }
});
