
App.controller('AdminUsersController', /*@ngInject*/ function ($scope, UserManagementAdmin, OrderByMixin, _) {
    angular.extend($scope, OrderByMixin);

    $scope.rights  = [];
    $scope.users   = [];
    $scope.orderBy = 'username';

    UserManagementAdmin.getUsers().then(function(data) {
        $scope.rights = data.data.rights;
        $scope.users  = data.data.users;
    });

    $scope.save = function(user) {
        UserManagementAdmin.edit(user).then(function(newUser) {
            user.edit = false;
        });
    };

    $scope.newPassword = function(user) {
        user.password = prompt(_('Password'));

        UserManagementAdmin.edit(user);
    };
});
