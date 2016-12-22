
App.controller('UserController', /*@ngInject*/ function ($scope, UserManagement, UserManagementAvatar) {
    $scope.user = null;
    $scope.avatars = null;

    UserManagement.loadCurrentUser().then(function(user) {
        $scope.user = user.data;
    });

    UserManagementAvatar.getList().then(function(avatars) {
        $scope.avatars = avatars.data;
    });

    $scope.setAvatar = function (avatar) {
        UserManagementAvatar.set(avatar).then(function(user) {
            UserManagement.clearCache();
            UserManagement.setCurrentUser(user.data);
            $scope.user = user.data;
        });
    };

    $scope.changeEmail = function () {
        UserManagement.changeEmail($scope.user.email).then(function(result) {
            let user = result.data;
            UserManagement.setCurrentUser(user);
            $scope.user = user;
        });
    };
});
