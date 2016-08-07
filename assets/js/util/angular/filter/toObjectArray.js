
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
