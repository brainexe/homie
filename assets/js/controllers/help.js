
App.controller('HelpController', ['$scope', '$routeParams', 'Help', function ($scope, $routeParams, Help) {
    $scope.help = {};

    Help.getAll().success(function (data) {
        $scope.help = data;
    });

    $scope.save = function(type, content) {
        $scope[type] = content;
        Help.save(type, content);
    };
}]);
