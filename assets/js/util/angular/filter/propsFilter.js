
App.filter('propsFilter', function () {
    return function(items, props) {
        return items.filter(function(item) {
            var keys = Object.keys(props);
            for (var i = 0; i < keys.length; i++) {
                var prop = keys[i];
                var text = props[prop].toLowerCase();
                if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                    return true;
                }
            }
            return false;
        });
    }
});
