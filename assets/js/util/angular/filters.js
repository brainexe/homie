App.ng.filter('notEmpty', function () {
    return function (input) {
        if (!input) {
            return false;
        }

        return Object.keys(input).length > 0;
    };
});

App.ng.filter('orderObjectBy', function(){
    return function(input, attribute) {
        if (!angular.isObject(input)) return input;

        var array = [];
        for(var objectKey in input) {
            array.push(input[objectKey]);
        }

        array.sort(function(a, b){
            a = parseInt(a[attribute]);
            b = parseInt(b[attribute]);
            return a - b;
        });
        return array;
    }
});
