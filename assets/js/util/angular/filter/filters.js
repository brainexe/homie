
App.filter('toArray', /*@ngInject*/ function(lodash) {
    return lodash.toArray;
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

App.filter('join', /*@ngInject*/ function(lodash) {
    return lodash.join;
});
