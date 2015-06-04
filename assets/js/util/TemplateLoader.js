
App.service('TemplateLoader', ['$http', function ($http) {
    return function (file) {
        return $http.get(file, {cache: true});
    };
}]);
