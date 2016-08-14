
App.filter('propsFilter', function () {
    return function(items, props) {
        return items.filter(function(item) {
            var keys = Object.keys(props);
            var i;

            for (i = 0; i < keys.length; i++) {
                var prop = keys[i];
                var text = props[prop].toLowerCase();
                if (item[prop].toString().toLowerCase().includes(text)) {
                    return true;
                }
            }
            return false;
        });
    }
});
