
App.filter('toArray', /*@ngInject*/ (lodash) => lodash.toArray);
App.filter('join',    /*@ngInject*/ (lodash) => lodash.join);

App.filter('deleteKey', function() {
    return function(array, key) {
        delete array[key];

        return array;
    };
});

/**
 * http://stackoverflow.com/questions/17448100/how-to-split-a-string-with-angularjs
 */
App.filter('split', function() {
    return function(input, splitChar, splitIndex) {
        // do some bounds checking here to ensure it has that index
        return input.split(splitChar)[splitIndex];
    };
});

