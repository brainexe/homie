
App.controller('AdminUsersController', /*@ngInject*/ function ($scope, UserManagementAdmin, OrderByMixin, _) {
    angular.extend($scope, OrderByMixin);

    $scope.rights  = [];
    $scope.users   = [];
    $scope.orderBy = 'username';

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
    };
});
