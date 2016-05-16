
/**
 * @returns String {string}
 */
String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined'
            ? args[number]
            : match;
    });
};

/**
 * @source http://stackoverflow.com/questions/5767325/remove-a-specific-element-from-an-array-in-javascript
 * @param value
 * @returns {Array}
 * @deprecated todo remove
 */
removeByValue = function(array, value, key) {
    for (var i = 0; i < array.length; i++) {
        if (array[i] === value || (key && value[key] == array[i][key])) {
            array.splice(i, 1);
            i--;
        }
    }
};
