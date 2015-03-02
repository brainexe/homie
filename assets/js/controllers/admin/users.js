
App.ng.controller('AdminUsersController', ['$scope', function ($scope) {

    $.get('/admin/users/', function(data) {
        $scope.users = data.users;
        $scope.$apply();
    });

    $scope.save = function(user) {
        $.post('/admin/users/edit/', user, function(newUser) {
            user.edit = false;
            $scope.$apply();
        });
    };
}]);
