
App.controller('UserController', ['$scope', 'UserManagement', 'UserManagement.Avatar', function ($scope, UserManagement, Avatar) {

    $scope.user = UserManagement.getCurrentUser();

    Avatar.getList().success(function(avatars) {
        $scope.avatars = avatars;
    });

    $scope.setAvatar = function (avatar) {
        Avatar.set(avatar).success(function(user) {
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
}]);
