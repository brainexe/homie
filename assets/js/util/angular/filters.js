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

App.filter('orderObjectBy', function() {
    return function(input, attribute) {
        if (!angular.isObject(input)) return input;

        var array = [];
        for (var objectKey in input) {
            array.push(input[objectKey]);
        }

        var reversed = false;
        if (attribute.charAt(0) == '-') {
            attribute = attribute.substring(1);
            reversed = true;
        }

        array.sort(function(a, b){
            a = parseInt(a[attribute]);
            b = parseInt(b[attribute]);
            return a - b;
        });

        if (reversed) {
            array = array.reverse();
        }

        return array;
    }
});

/**
 * http://stackoverflow.com/questions/17635866/get-values-from-object-in-javascript
 */
App.filter('toArray', function(){
    return function(input) {
        if (!input) {
            return [];
        }

        return Object.keys(input).map(function(key){
            return input[key]
        });
    }
});
App.filter('deleteKey', function() {
    return function(array, key) {
        delete array[key];

        return array;
    }
});

App.filter('toObjectArray', function() {
    return function(input) {
        if (!input) {
            return [];
        }
        if (input.$$toObjectArray) {
            return input.$$toObjectArray;
        }

        return input.$$toObjectArray = Object.keys(input).map(function(key){
            return {
                key :  key,
                value: input[key]
            }
        });
    }
});

App.filter('propsFilter', function() {
    return function(items, props) {
        var out = [];

        if (angular.isArray(items)) {
            items.forEach(function(item) {
                var itemMatches = false;

                var keys = Object.keys(props);
                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }

                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            // Let the output be the input untouched
            out = items;
        }

        return out;
    }
});

App.filter('filterArray', function() {
    return function(items, needle) {
        if (needle || !items) {
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

/**
 * http://stackoverflow.com/questions/17448100/how-to-split-a-string-with-angularjs
 */
App.filter('split', function() {
    return function(input, splitChar, splitIndex) {
        // do some bounds checking here to ensure it has that index
        return input.split(splitChar)[splitIndex];
    }
});

App.filter('join', function () {
    return function (input, delimiter) {
        if (!Array.isArray(input)) {
            return input;
        }
        return input.join(delimiter || ' ');
    };
});

App.filter('range', function () {
    return function (input, total) {
        for (var i = 0; i < ~~total; i++) {
            input.push(i);
        }
        return input;
    };
});
