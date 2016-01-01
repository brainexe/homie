
App.controller('UserController', ['$scope', 'UserManagement', 'UserManagement.Avatar', 'controllers', function ($scope, UserManagement, Avatar, controllers) {

    $scope.user = UserManagement.getCurrentUser();

    $scope.controllers = controllers().filter(function(controller) {
        return controller.collapsible
    });

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

    $scope.toggleMenu = function(controller) {
        // TODO
    };
}]);
