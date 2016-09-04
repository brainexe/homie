
App.controller('UserController', /*@ngInject*/ function ($scope, UserManagement, UserManagementAvatar) {
    $scope.user = null;
    $scope.avatars = null;

    UserManagement.loadCurrentUser().success(function(user) {
        $scope.user = user;
    });

    UserManagementAvatar.getList().success(function(avatars) {
        $scope.avatars = avatars;
    });

    $scope.setAvatar = function (avatar) {
        UserManagementAvatar.set(avatar).success(function(user) {
            UserManagement.clearCache();
            UserManagement.setCurrentUser(user);
            $scope.user = user;
        });
    };

    $scope.changeEmail = function () {
        UserManagement.changeEmail($scope.user.email).success(function(user) {
            UserManagement.setCurrentUser(user);
            $scope.user = user;
        });
    };
});
