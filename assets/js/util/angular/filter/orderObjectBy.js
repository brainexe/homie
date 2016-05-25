
App.filter('orderObjectBy', function() {
    return function(input, attribute) {
        if (!angular.isObject(input)) {
            return input;
        }

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
