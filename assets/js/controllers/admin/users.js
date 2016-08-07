
App.controller('AdminUsersController', /*@ngInject*/ function ($scope, UserManagementAdmin, _) {
    $scope.rights = [];
    $scope.users  = [];

    UserManagementAdmin.getUsers().success(function(data) {
        $scope.rights = data.rights;
        $scope.users  = data.users;
    });

    $scope.save = function(user) {
        UserManagementAdmin.edit(user).success(function(newUser) {
            user.edit = false;
        });
    };

    $scope.newPassword = function(user) {
        user.password = prompt(_('Password'));

        UserManagementAdmin.edit(user);
    }
});
