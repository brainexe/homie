
App.service('TemplateLoader', ["$http", "Cache", function ($http, Cache) {
    return (file) => $http.get(file, {cache: Cache});
}]);
