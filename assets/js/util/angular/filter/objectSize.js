
App.filter('objectSize', function() {
    return function(object) {
        var count = 0;

        for (var i in object){
            count++;
        }

        return count;
    };
});
