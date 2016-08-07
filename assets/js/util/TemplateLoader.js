
App.service('TemplateLoader', /*@ngInject*/ function ($http, Cache) {
    return function (file) {
        return $http.get(file, {cache: Cache});
    };
});
