
App.controller('AdminUsersController', ['$scope', 'UserManagement.Admin', function ($scope, Admin) {

    $scope.rights = [];
    $scope.users  = [];

    Admin.getUsers().success(function(data) {
        $scope.rights = data.rights;
        $scope.users  = data.users;
    });

    $scope.save = function(user) {
        Admin.edit(user).success(function(newUser) {
            user.edit = false;
        });
    };

    $scope.newPassword = function(user) {
        user.password = prompt('Password');

        Admin.edit(user);
    }
}]);
