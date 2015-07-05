
App.service('TemplateLoader', ['$http', 'Cache', function ($http, Cache) {
    return function (file) {
        return $http.get(file, {cache: Cache});
    };
}]);
