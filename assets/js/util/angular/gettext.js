
App.service('_', ['gettextCatalog', function (gettextCatalog) {
    return function(string) {
        return gettextCatalog.getString(string);
    }
}]);
