
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
