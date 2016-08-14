
/**
 * @returns String {string}
 * @deprecated use `string ${param}`
 */
String.prototype.format = function () {
    var args = arguments;

    return this.replace(/{(\d+)}/g, function (match, number) {
        return args[number] != undefined
            ? args[number]
            : match;
    });
};
