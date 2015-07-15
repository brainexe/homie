
/**
 * Pseudo function needed to mark asset links. during console assets:dump the links are replaced by hashed ones
 * @param {String} filename
 * @deprecated
 * @returns {String}
 */
function asset(filename) {
    return filename;
}

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
