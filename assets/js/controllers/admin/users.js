
App.ng.controller('AdminUsersController', ['$scope', function ($scope) {

    $scope.rights = [];
    $scope.users  = [];

    $.get('/admin/users/', function(data) {
        $scope.rights = data.rights;
        $scope.users  = data.users;
        $scope.$apply();
    });

    $scope.save = function(user) {
        $.post('/admin/users/edit/', user, function(newUser) {
            user.edit = false;
            $scope.$apply();
        });
    };

    $scope.newPassword = function(user) {
        var password = prompt('Password');
        user.password = password;
        $.post('/admin/users/edit/', user, function() {
        });
    }
}]);
